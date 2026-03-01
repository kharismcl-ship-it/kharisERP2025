<?php

namespace Modules\HR\Services;

use Illuminate\Support\Collection;
use Modules\HR\Models\AllowanceType;
use Modules\HR\Models\DeductionType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSalary;
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
            'total_gross'      => $run->payrollLines->sum('gross_salary'),
            'total_net'        => $run->payrollLines->sum('net_salary'),
            'total_paye'       => $run->payrollLines->sum('paye_tax'),
            'total_ssnit'      => $run->payrollLines->sum('ssnit_employee'),
            'employee_count'   => $run->payrollLines->count(),
        ]);

        return $run;
    }

    /**
     * Calculate payroll for a single employee within a run.
     */
    public function calculateEmployeePayroll(PayrollRun $run, Employee $employee): PayrollLine
    {
        $salary = EmployeeSalary::where('employee_id', $employee->id)
            ->where('effective_date', '<=', now())
            ->orderByDesc('effective_date')
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
     * Post finalized payroll totals to Finance (stub — implement when Finance integration ready).
     */
    public function postToFinance(PayrollRun $run): void
    {
        // TODO: create Journal Entry in Finance module for total payroll cost
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

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