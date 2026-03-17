<?php

namespace Modules\HR\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\HR\Models\AllowanceType;
use Modules\HR\Models\BenefitType;
use Modules\HR\Models\DeductionType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeBenefit;
use Modules\HR\Models\EmployeeLoan;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Models\LoanRepayment;
use Modules\HR\Models\PayrollLine;
use Modules\HR\Models\PayrollRun;

class PayrollService
{
    /**
     * Generate a new payroll run for a company and period.
     */
    public function generatePayrollRun(int $companyId, int $year, int $month): PayrollRun
    {
        $run = PayrollRun::create([
            'company_id'   => $companyId,
            'period_year'  => $year,
            'period_month' => $month,
            'status'       => 'draft',
            'created_by'   => auth()->id(),
        ]);

        $employees = Employee::where('company_id', $companyId)
            ->where('employment_status', 'active')
            ->get();

        foreach ($employees as $employee) {
            $this->calculateEmployeePayroll($run, $employee);
        }

        $run->refresh();
        $run->update([
            'total_gross'      => $run->lines->sum('gross_salary'),
            'total_deductions' => $run->lines->sum('total_deductions'),
            'total_net'        => $run->lines->sum('net_salary'),
            'total_paye'       => $run->lines->sum('paye_tax'),
            'total_ssnit'      => $run->lines->sum('ssnit_employee'),
            'employee_count'   => $run->lines->count(),
        ]);

        return $run;
    }

    /**
     * Process (or reprocess) an existing draft PayrollRun.
     * Deletes any existing lines, recalculates for all active employees,
     * updates run totals, and sets status to 'processing'.
     */
    public function processExistingRun(PayrollRun $run): PayrollRun
    {
        // Wipe any previously generated lines so we can start fresh
        $run->lines()->delete();

        $employees = Employee::where('company_id', $run->company_id)
            ->where('employment_status', 'active')
            ->get();

        foreach ($employees as $employee) {
            $this->calculateEmployeePayroll($run, $employee);
        }

        $run->refresh();
        $run->update([
            'status'           => 'processing',
            'total_gross'      => $run->lines->sum('gross_salary'),
            'total_deductions' => $run->lines->sum('total_deductions'),
            'total_net'        => $run->lines->sum('net_salary'),
            'total_paye'       => $run->lines->sum('paye_tax'),
            'total_ssnit'      => $run->lines->sum('ssnit_employee'),
            'employee_count'   => $run->lines->count(),
        ]);

        return $run;
    }

    /**
     * Calculate payroll for a single employee within a run.
     */
    public function calculateEmployeePayroll(PayrollRun $run, Employee $employee): PayrollLine
    {
        $salary = EmployeeSalary::where('employee_id', $employee->id)
            ->where('effective_from', '<=', now())
            ->orderByDesc('effective_from')
            ->first();

        $basicSalary = $salary?->basic_salary ?? 0;

        $allowanceTypes = AllowanceType::where('company_id', $run->company_id)
            ->where('is_active', true)
            ->get();

        $deductionTypes = DeductionType::where('company_id', $run->company_id)
            ->where('is_active', true)
            ->get();

        $allowances = $this->computeAllowances($allowanceTypes, $basicSalary);
        $deductions = $this->computeDeductions($deductionTypes, $basicSalary);

        // Loan deductions
        $loanDeductions = $this->computeLoanDeductions($employee, $run);
        $deductions = array_merge($deductions, $loanDeductions);

        // Benefit employee contributions
        $benefitDeductions = $this->computeBenefitDeductions($employee);
        $deductions = array_merge($deductions, $benefitDeductions);

        $grossSalary   = $basicSalary + array_sum(array_column($allowances, 'amount'));
        $ssnitEmployee = round($basicSalary * 0.055, 2);   // 5.5%
        $ssnitEmployer = round($basicSalary * 0.13, 2);    // 13%
        $payeTax       = $this->calculatePAYE($grossSalary - $ssnitEmployee);
        $totalDeductions = array_sum(array_column($deductions, 'amount')) + $ssnitEmployee + $payeTax;
        $netSalary     = $grossSalary - $totalDeductions;

        return PayrollLine::create([
            'payroll_run_id'    => $run->id,
            'employee_id'       => $employee->id,
            'basic_salary'      => $basicSalary,
            'gross_salary'      => $grossSalary,
            'total_allowances'  => round(array_sum(array_column($allowances, 'amount')), 2),
            'total_deductions'  => round($totalDeductions, 2),
            'net_salary'        => $netSalary,
            'paye_tax'          => $payeTax,
            'ssnit_employee'    => $ssnitEmployee,
            'ssnit_employer'    => $ssnitEmployer,
            'allowances'        => $allowances,
            'deductions'        => $deductions,
        ]);
    }

    /**
     * Calculate PAYE tax using Ghana PAYE bands (2024/2025).
     */
    public function calculatePAYE(float $taxableIncome): float
    {
        // Monthly PAYE bands (GHS)
        $bands = [
            ['limit' => 319,  'rate' => 0.00],
            ['limit' => 110,  'rate' => 0.05],
            ['limit' => 130,  'rate' => 0.10],
            ['limit' => 3000, 'rate' => 0.175],
            ['limit' => 16840,'rate' => 0.25],
            ['limit' => null,  'rate' => 0.30],
        ];

        $paye = 0;
        $remaining = $taxableIncome;

        foreach ($bands as $band) {
            if ($remaining <= 0) {
                break;
            }
            $taxable = $band['limit'] !== null ? min($remaining, $band['limit']) : $remaining;
            $paye += $taxable * $band['rate'];
            $remaining -= $taxable;
        }

        return round($paye, 2);
    }

    /**
     * Calculate SSNIT contribution.
     */
    public function calculateSSNIT(float $basicSalary): array
    {
        return [
            'employee' => round($basicSalary * 0.055, 2),
            'employer' => round($basicSalary * 0.13, 2),
        ];
    }

    /**
     * Finalize a payroll run (locks it from further edits).
     */
    public function finalizePayrollRun(PayrollRun $run): PayrollRun
    {
        $run->update([
            'status'       => 'finalized',
            'finalized_at' => now(),
            'finalized_by' => auth()->id(),
        ]);

        return $run;
    }

    /**
     * Post finalized payroll to Finance as a Journal Entry.
     *
     * Debit entries:
     *   5210  Salaries & Wages       = total gross pay
     *   5220  SSNIT Employer Contrib = total SSNIT employer portion
     *
     * Credit entries:
     *   2120  Accrued Liabilities    = total net pay (salary payable to employees)
     *   2140  Income Tax Payable     = total PAYE tax
     *   2150  Pension Payable (SSNIT)= SSNIT employee + SSNIT employer
     */
    public function postToFinance(PayrollRun $run): JournalEntry
    {
        $lines = $run->lines();

        $totalGross        = (float) $lines->sum('gross_salary');
        $totalNet          = (float) $lines->sum('net_salary');
        $totalPaye         = (float) $lines->sum('paye_tax');
        $totalSsnitEmployee= (float) $lines->sum('ssnit_employee');
        $totalSsnitEmployer= (float) $lines->sum('ssnit_employer');
        $totalSsnit        = round($totalSsnitEmployee + $totalSsnitEmployer, 2);

        $month  = str_pad($run->period_month, 2, '0', STR_PAD_LEFT);
        $ref    = "PAY-{$run->period_year}-{$month}";
        $desc   = "Payroll for {$ref} ({$run->employee_count} employees)";

        // Resolve GL account IDs by standard chart-of-accounts codes
        $accountMap = Account::where('company_id', $run->company_id)
            ->whereIn('code', ['5210', '5220', '2120', '2140', '2150'])
            ->get()
            ->keyBy('code');

        $entry = JournalEntry::create([
            'company_id'  => $run->company_id,
            'date'        => now()->toDateString(),
            'reference'   => $ref,
            'description' => $desc,
            'is_locked'   => false,
        ]);

        // Helper to create a line (skips if account not found and logs a warning)
        $addLine = function (string $code, float $debit, float $credit) use ($entry, $accountMap): void {
            $account = $accountMap->get($code);

            if (! $account) {
                Log::warning("PayrollService::postToFinance — account code '{$code}' not found for company {$entry->company_id}. Journal line skipped.");

                return;
            }

            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $account->id,
                'debit'            => $debit,
                'credit'           => $credit,
            ]);
        };

        // Debit lines
        $addLine('5210', $totalGross,        0);
        $addLine('5220', $totalSsnitEmployer, 0);

        // Credit lines
        $addLine('2120', 0, $totalNet);
        $addLine('2140', 0, $totalPaye);
        $addLine('2150', 0, $totalSsnit);

        return $entry;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Compute loan repayment deductions for an employee and create LoanRepayment records.
     */
    private function computeLoanDeductions(Employee $employee, PayrollRun $run): array
    {
        $activeLoans = EmployeeLoan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('outstanding_balance', '>', 0)
            ->get();

        $deductions = [];

        foreach ($activeLoans as $loan) {
            $deductionAmount = min((float) $loan->monthly_deduction, (float) $loan->outstanding_balance);

            if ($deductionAmount <= 0) {
                continue;
            }

            $outstandingBefore = (float) $loan->outstanding_balance;
            $outstandingAfter  = round($outstandingBefore - $deductionAmount, 2);

            LoanRepayment::create([
                'employee_loan_id'   => $loan->id,
                'payment_date'       => now()->toDateString(),
                'amount'             => $deductionAmount,
                'outstanding_before' => $outstandingBefore,
                'outstanding_after'  => $outstandingAfter,
                'payment_method'     => 'payroll_deduction',
                'payroll_run_id'     => $run->id,
            ]);

            // Update loan outstanding balance; mark cleared if fully repaid
            $loan->outstanding_balance = $outstandingAfter;
            if ($outstandingAfter <= 0) {
                $loan->status = 'cleared';
            }
            $loan->save();

            $deductions[] = [
                'name'   => "Loan Repayment ({$loan->loan_type})",
                'amount' => $deductionAmount,
            ];
        }

        return $deductions;
    }

    /**
     * Compute benefit employee contribution deductions.
     */
    private function computeBenefitDeductions(Employee $employee): array
    {
        $activeBenefits = EmployeeBenefit::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->with('benefitType')
            ->get();

        $deductions = [];

        foreach ($activeBenefits as $benefit) {
            $type = $benefit->benefitType;
            if (! $type || ! $type->employee_contribution_required) {
                continue;
            }

            $amount = $benefit->employee_contribution_override !== null
                ? (float) $benefit->employee_contribution_override
                : (float) $type->employee_contribution;

            if ($amount <= 0) {
                continue;
            }

            $deductions[] = [
                'name'   => "{$type->name} (Employee Contribution)",
                'amount' => round($amount, 2),
            ];
        }

        return $deductions;
    }

    private function computeAllowances(Collection $types, float $basicSalary): array
    {
        return $types->map(function (AllowanceType $type) use ($basicSalary) {
            $amount = $type->calculation_type === 'percentage'
                ? round($basicSalary * ($type->percentage_value / 100), 2)
                : ($type->default_amount ?? 0);

            return ['name' => $type->name, 'amount' => $amount];
        })->values()->toArray();
    }

    private function computeDeductions(Collection $types, float $basicSalary): array
    {
        return $types->map(function (DeductionType $type) use ($basicSalary) {
            $amount = $type->calculation_type === 'percentage'
                ? round($basicSalary * ($type->percentage_value / 100), 2)
                : ($type->default_amount ?? 0);

            return ['name' => $type->name, 'amount' => $amount];
        })->values()->toArray();
    }
}