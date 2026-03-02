<?php

use App\Models\Company;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\HR\Models\AllowanceType;
use Modules\HR\Models\DeductionType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Models\PayrollLine;
use Modules\HR\Models\PayrollRun;
use Modules\HR\Services\PayrollService;

beforeEach(function () {
    $this->service = app(PayrollService::class);
    $this->company = Company::factory()->create();
});

// ── PAYE CALCULATION ─────────────────────────────────────────────────────────

test('PAYE is zero below the first band threshold (GHS 319)', function () {
    expect($this->service->calculatePAYE(300))->toBe(0.0);
});

test('PAYE applies 5% on income in the 2nd band (GHS 319-429)', function () {
    // 319 free + 100 at 5% = 5.00
    expect($this->service->calculatePAYE(419))->toBe(5.0);
});

test('PAYE applies correct bands for mid-range income', function () {
    // 319 free + 110 @ 5% = 5.50 + 130 @ 10% = 13.00 + 441 @ 17.5% = 77.175
    // Total income = 319 + 110 + 130 + 441 = 1000
    $paye = $this->service->calculatePAYE(1000);
    expect($paye)->toBe(round(0 + (110 * 0.05) + (130 * 0.10) + (441 * 0.175), 2));
});

test('PAYE calculation is deterministic for the same input', function () {
    $paye1 = $this->service->calculatePAYE(5000);
    $paye2 = $this->service->calculatePAYE(5000);
    expect($paye1)->toBe($paye2);
});

// ── SSNIT CALCULATION ────────────────────────────────────────────────────────

test('SSNIT employee contribution is 5.5% of basic salary', function () {
    $result = $this->service->calculateSSNIT(2000.0);
    expect($result['employee'])->toBe(round(2000 * 0.055, 2));
});

test('SSNIT employer contribution is 13% of basic salary', function () {
    $result = $this->service->calculateSSNIT(2000.0);
    expect($result['employer'])->toBe(round(2000 * 0.13, 2));
});

// ── GENERATE PAYROLL RUN ─────────────────────────────────────────────────────

test('it generates a payroll run for active employees', function () {
    $employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    EmployeeSalary::create([
        'employee_id'    => $employee->id,
        'company_id'     => $this->company->id,
        'basic_salary'   => 3000.00,
        'effective_from' => now()->subMonth()->toDateString(),
        'is_current'     => true,
    ]);

    $run = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);

    expect($run)->toBeInstanceOf(PayrollRun::class);
    expect($run->status)->toBe('draft');
    expect($run->employee_count)->toBe(1);
    expect(PayrollLine::where('payroll_run_id', $run->id)->count())->toBe(1);
});

test('it skips inactive employees in payroll run', function () {
    Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'inactive',
    ]);

    $run = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);

    expect($run->employee_count)->toBe(0);
});

test('it calculates gross salary including fixed allowances', function () {
    $employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    EmployeeSalary::create([
        'employee_id'    => $employee->id,
        'company_id'     => $this->company->id,
        'basic_salary'   => 2000.00,
        'effective_from' => now()->subMonth()->toDateString(),
        'is_current'     => true,
    ]);

    AllowanceType::create([
        'company_id'       => $this->company->id,
        'name'             => 'Transport',
        'code'             => 'TRANS',
        'calculation_type' => 'fixed',
        'default_amount'   => 500.00,
        'is_taxable'       => true,
        'is_pensionable'   => false,
        'is_active'        => true,
    ]);

    $run  = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);
    $line = PayrollLine::where('payroll_run_id', $run->id)->first();

    expect((float) $line->gross_salary)->toBe(2500.0); // 2000 + 500
});

test('net salary equals gross minus statutory deductions', function () {
    $employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    $basic = 4000.00;
    EmployeeSalary::create([
        'employee_id'    => $employee->id,
        'company_id'     => $this->company->id,
        'basic_salary'   => $basic,
        'effective_from' => now()->subMonth()->toDateString(),
        'is_current'     => true,
    ]);

    $run  = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);
    $line = PayrollLine::where('payroll_run_id', $run->id)->first();

    $ssnitEmployee = round($basic * 0.055, 2);
    $paye          = $this->service->calculatePAYE($basic - $ssnitEmployee);
    $expectedNet   = $basic - $ssnitEmployee - $paye;

    expect(round((float) $line->net_salary, 2))->toBe(round($expectedNet, 2));
});

// ── FINALIZE PAYROLL RUN ─────────────────────────────────────────────────────

test('it finalizes a payroll run', function () {
    $run = PayrollRun::create([
        'company_id'   => $this->company->id,
        'period_year'  => now()->year,
        'period_month' => now()->month,
        'status'       => 'draft',
        'created_by'   => null,
    ]);

    $finalized = $this->service->finalizePayrollRun($run);

    expect($finalized->status)->toBe('finalized');
    expect($finalized->finalized_at)->not->toBeNull();
});

// ── POST TO FINANCE ───────────────────────────────────────────────────────────

test('postToFinance creates a journal entry for the payroll period', function () {
    $employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    EmployeeSalary::create([
        'employee_id'    => $employee->id,
        'company_id'     => $this->company->id,
        'basic_salary'   => 3000.00,
        'effective_from' => now()->subMonth()->toDateString(),
        'is_current'     => true,
    ]);

    // Seed the GL accounts the service looks for
    $accountData = [
        ['code' => '5210', 'name' => 'Salaries & Wages',            'type' => 'expense'],
        ['code' => '5220', 'name' => 'SSNIT Employer Contribution', 'type' => 'expense'],
        ['code' => '2120', 'name' => 'Accrued Liabilities',         'type' => 'liability'],
        ['code' => '2140', 'name' => 'Income Tax Payable',          'type' => 'liability'],
        ['code' => '2150', 'name' => 'Pension Payable (SSNIT)',     'type' => 'liability'],
    ];
    foreach ($accountData as $a) {
        Account::create(['company_id' => $this->company->id] + $a);
    }

    $run   = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);
    $entry = $this->service->postToFinance($run);

    expect($entry)->toBeInstanceOf(JournalEntry::class);
    expect($entry->company_id)->toBe($this->company->id);
    expect($entry->reference)->toBe('PAY-' . now()->year . '-' . str_pad(now()->month, 2, '0', STR_PAD_LEFT));

    // Should have 5 lines (2 debits + 3 credits)
    expect(JournalLine::where('journal_entry_id', $entry->id)->count())->toBe(5);
});

test('postToFinance debits balance equals credits balance', function () {
    $employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    EmployeeSalary::create([
        'employee_id'    => $employee->id,
        'company_id'     => $this->company->id,
        'basic_salary'   => 5000.00,
        'effective_from' => now()->subMonth()->toDateString(),
        'is_current'     => true,
    ]);

    $accountData = [
        ['code' => '5210', 'name' => 'Salaries & Wages',            'type' => 'expense'],
        ['code' => '5220', 'name' => 'SSNIT Employer Contribution', 'type' => 'expense'],
        ['code' => '2120', 'name' => 'Accrued Liabilities',         'type' => 'liability'],
        ['code' => '2140', 'name' => 'Income Tax Payable',          'type' => 'liability'],
        ['code' => '2150', 'name' => 'Pension Payable (SSNIT)',     'type' => 'liability'],
    ];
    foreach ($accountData as $a) {
        Account::create(['company_id' => $this->company->id] + $a);
    }

    $run   = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);
    $entry = $this->service->postToFinance($run);

    $lines       = JournalLine::where('journal_entry_id', $entry->id)->get();
    $totalDebit  = $lines->sum('debit');
    $totalCredit = $lines->sum('credit');

    expect(round((float) $totalDebit, 2))->toBe(round((float) $totalCredit, 2));
});

test('postToFinance still creates entry even when GL accounts are missing', function () {
    $employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    EmployeeSalary::create([
        'employee_id'    => $employee->id,
        'company_id'     => $this->company->id,
        'basic_salary'   => 2000.00,
        'effective_from' => now()->subMonth()->toDateString(),
        'is_current'     => true,
    ]);

    $run = $this->service->generatePayrollRun($this->company->id, now()->year, now()->month);

    // No GL accounts — should still create the journal entry header
    $entry = $this->service->postToFinance($run);

    expect($entry)->toBeInstanceOf(JournalEntry::class);
    // But no lines will be created (accounts not found)
    expect(JournalLine::where('journal_entry_id', $entry->id)->count())->toBe(0);
});
