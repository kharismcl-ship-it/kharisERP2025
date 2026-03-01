<?php

use App\Models\Company;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;
use Modules\HR\Services\LeaveAccrualService;

beforeEach(function () {
    // Use existing company instead of creating new one
    $this->company = Company::find(1); // Kharis Hostels
    $this->employee = Employee::factory()->create([
        'company_id' => $this->company->id,
        'employment_status' => 'active',
        'employment_type' => 'full_time',
        'joining_date' => now()->subYear(),
    ]);

    $this->leaveType = LeaveType::factory()->create([
        'company_id' => $this->company->id,
        'name' => 'Annual Leave',
        'has_accrual' => true,
        'accrual_rate' => 1.67, // 20 days per year
        'accrual_frequency' => 'monthly',
        'pro_rata_enabled' => true,
        'carryover_limit' => 10.0,
        'max_balance' => 30.0,
    ]);

    $this->accrualService = app(LeaveAccrualService::class);
});

test('it calculates monthly pro rata for new hires', function () {
    $newEmployee = Employee::factory()->create([
        'company_id' => $this->company->id,
        'joining_date' => now()->subDays(15), // Joined 15 days ago
    ]);

    $accrualDate = now();
    $rate = $this->accrualService->calculateProRataAccrual($newEmployee, $this->leaveType, $accrualDate);

    // Should accrue for 15/30 days of the month
    $expectedRate = 1.67 * (15 / 30);
    expect(round($rate, 2))->toBe(round($expectedRate, 2));
});

test('it handles part time employee adjustments', function () {
    $partTimeEmployee = Employee::factory()->create([
        'company_id' => $this->company->id,
        'employment_type' => 'part_time',
        'standard_hours_per_week' => 20, // Half of full-time
    ]);

    $accrualDate = now();
    $rate = $this->accrualService->calculateProRataAccrual($partTimeEmployee, $this->leaveType, $accrualDate);

    // Should be half of full-time rate
    $expectedRate = 1.67 * (20 / 40);
    expect(round($rate, 2))->toBe(round($expectedRate, 2));
});

test('it processes year end carry over correctly', function () {
    // Create a balance with 15 days remaining
    $balance = LeaveBalance::factory()->create([
        'employee_id' => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'year' => 2023,
        'current_balance' => 15.0,
        'carryover_limit' => 10.0,
    ]);

    $results = $this->accrualService->processYearEndCarryOver(2023, 2024);

    expect($results['employees_processed'])->toBe(1);
    expect($results['balances_updated'])->toBe(1);
    expect($results['errors'])->toBeEmpty();

    // Check that new balance was created with carry-over
    $newBalance = LeaveBalance::where('employee_id', $this->employee->id)
        ->where('leave_type_id', $this->leaveType->id)
        ->where('year', 2024)
        ->first();

    expect($newBalance)->not->toBeNull();
    expect($newBalance->carried_over)->toBe(10.0); // Limited by carryover limit
});

test('it respects maximum balance cap', function () {
    $balance = LeaveBalance::factory()->create([
        'employee_id' => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'year' => 2023,
        'current_balance' => 25.0, // High balance
    ]);

    // Set max balance to 30, initial balance for new year is 20
    $this->leaveType->update(['max_balance' => 30.0]);

    $results = $this->accrualService->processYearEndCarryOver(2023, 2024);

    $newBalance = LeaveBalance::where('employee_id', $this->employee->id)
        ->where('leave_type_id', $this->leaveType->id)
        ->where('year', 2024)
        ->first();

    // Should carry over 10 days (30 max - 20 initial)
    expect($newBalance->carried_over)->toBe(10.0);
});

test('it generates accrual forecast', function () {
    // Create current year balance
    $balance = LeaveBalance::factory()->create([
        'employee_id' => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'year' => now()->year,
        'current_balance' => 10.0,
    ]);

    $forecast = $this->accrualService->getAccrualForecast($this->employee);

    expect($forecast)->toHaveCount(1);
    expect($forecast[0]['leave_type'])->toBe('Annual Leave');
    expect($forecast[0]['current_balance'])->toBe(10.0);
    expect($forecast[0]['accrual_rate'])->toBe(1.67);
});

test('it handles terminated employees', function () {
    $terminatedEmployee = Employee::factory()->create([
        'company_id' => $this->company->id,
        'employment_status' => 'terminated',
        'termination_date' => now()->subMonth(),
    ]);

    $accrualDate = now();
    $rate = $this->accrualService->calculateProRataAccrual($terminatedEmployee, $this->leaveType, $accrualDate);

    // Terminated employees should not accrue leave
    expect($rate)->toBe(0.0);
});

test('it handles different accrual frequencies', function () {
    $quarterlyType = LeaveType::factory()->create([
        'company_id' => $this->company->id,
        'accrual_rate' => 5.0, // 5 days per quarter
        'accrual_frequency' => 'quarterly',
    ]);

    $annualType = LeaveType::factory()->create([
        'company_id' => $this->company->id,
        'accrual_rate' => 20.0, // 20 days per year
        'accrual_frequency' => 'annually',
    ]);

    $quarterlyRate = $this->accrualService->calculateProRataAccrual($this->employee, $quarterlyType, now());
    $annualRate = $this->accrualService->calculateProRataAccrual($this->employee, $annualType, now());

    // Quarterly should be converted to monthly equivalent
    expect($quarterlyRate)->toBe(5.0 / 3);

    // Annual should be converted to monthly equivalent
    expect($annualRate)->toBe(20.0 / 12);
});
