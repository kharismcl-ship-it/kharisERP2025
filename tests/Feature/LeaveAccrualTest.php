<?php

use App\Models\Company;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;
use Modules\HR\Services\LeaveAccrualService;

beforeEach(function () {
    $this->company = Company::factory()->create();

    $this->employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
        'employment_type'   => 'full_time',
    ]);

    $this->leaveType = LeaveType::factory()->create([
        'company_id'        => $this->company->id,
        'name'              => 'Annual Leave',
        'has_accrual'       => true,
        'accrual_rate'      => 1.67,
        'accrual_frequency' => 'monthly',
        'pro_rata_enabled'  => true,
        'carryover_limit'   => 10.0,
        'max_balance'       => 30.0,
        'max_days_per_year' => 20,
    ]);

    $this->accrualService = app(LeaveAccrualService::class);
});

test('it returns full accrual rate when pro rata is disabled', function () {
    $leaveType = LeaveType::factory()->create([
        'company_id'       => $this->company->id,
        'accrual_rate'     => 1.67,
        'pro_rata_enabled' => false,
    ]);

    $rate = $this->accrualService->calculateProRataAccrual($this->employee, $leaveType, now());

    expect(round($rate, 2))->toBe(1.67);
});

test('it returns full accrual rate when employee has no joining date', function () {
    // Employee with no joining_date (null) and pro_rata enabled — service returns accrual_rate
    $rate = $this->accrualService->calculateProRataAccrual($this->employee, $this->leaveType, now());

    // employee has no joining_date set so condition `! $employee->joining_date` is true
    expect(round($rate, 2))->toBe(1.67);
});

test('it processes year end carry over correctly', function () {
    LeaveBalance::factory()->create([
        'company_id'      => $this->company->id,
        'employee_id'     => $this->employee->id,
        'leave_type_id'   => $this->leaveType->id,
        'year'            => 2023,
        'current_balance' => 15.0,
        'carried_over'    => 0.0,
    ]);

    $results = $this->accrualService->processYearEndCarryOver(2023, 2024);

    expect($results['employees_processed'])->toBe(1);
    expect($results['balances_updated'])->toBe(1);
    expect($results['errors'])->toBeEmpty();

    $newBalance = LeaveBalance::where('employee_id', $this->employee->id)
        ->where('leave_type_id', $this->leaveType->id)
        ->where('year', 2024)
        ->first();

    expect($newBalance)->not->toBeNull();
    // carryover_limit is 10, current_balance is 15 → carry over = min(15, 10) = 10
    expect((float) $newBalance->carried_over)->toBe(10.0);
});

test('it respects maximum balance cap during carry over', function () {
    LeaveBalance::factory()->create([
        'company_id'      => $this->company->id,
        'employee_id'     => $this->employee->id,
        'leave_type_id'   => $this->leaveType->id,
        'year'            => 2023,
        'current_balance' => 25.0,
        'carried_over'    => 0.0,
    ]);

    // max_balance = 30, max_days_per_year (initial) = 20 → max carry = 30 - 20 = 10
    $this->leaveType->update(['max_balance' => 30.0]);

    $results = $this->accrualService->processYearEndCarryOver(2023, 2024);

    $newBalance = LeaveBalance::where('employee_id', $this->employee->id)
        ->where('leave_type_id', $this->leaveType->id)
        ->where('year', 2024)
        ->first();

    expect((float) $newBalance->carried_over)->toBe(10.0);
});

test('it generates accrual forecast', function () {
    LeaveBalance::factory()->create([
        'company_id'      => $this->company->id,
        'employee_id'     => $this->employee->id,
        'leave_type_id'   => $this->leaveType->id,
        'year'            => now()->year,
        'current_balance' => 10.0,
    ]);

    $forecast = $this->accrualService->getAccrualForecast($this->employee);

    expect($forecast)->toHaveCount(1);
    expect($forecast[0]['leave_type'])->toBe('Annual Leave');
    expect((float) $forecast[0]['current_balance'])->toBe(10.0);
    expect((float) $forecast[0]['accrual_rate'])->toBe(1.67);
});

test('it skips carry over when carryover limit is zero', function () {
    $noCarryType = LeaveType::factory()->create([
        'company_id'       => $this->company->id,
        'has_accrual'      => true,
        'accrual_rate'     => 1.0,
        'accrual_frequency'=> 'monthly',
        'carryover_limit'  => 0.0,
        'max_balance'      => null,
        'max_days_per_year'=> 10,
    ]);

    LeaveBalance::factory()->create([
        'company_id'      => $this->company->id,
        'employee_id'     => $this->employee->id,
        'leave_type_id'   => $noCarryType->id,
        'year'            => 2023,
        'current_balance' => 8.0,
        'carried_over'    => 0.0,
    ]);

    $results = $this->accrualService->processYearEndCarryOver(2023, 2024);

    // No balance should be created for 2024 because carryover_limit = 0
    $newBalance = LeaveBalance::where('employee_id', $this->employee->id)
        ->where('leave_type_id', $noCarryType->id)
        ->where('year', 2024)
        ->first();

    expect($newBalance)->toBeNull();
    expect($results['employees_processed'])->toBe(1);
    expect($results['balances_updated'])->toBe(0);
});

test('it processes zero carry over when balance is zero', function () {
    LeaveBalance::factory()->create([
        'company_id'      => $this->company->id,
        'employee_id'     => $this->employee->id,
        'leave_type_id'   => $this->leaveType->id,
        'year'            => 2023,
        'current_balance' => 0.0,
        'carried_over'    => 0.0,
    ]);

    $results = $this->accrualService->processYearEndCarryOver(2023, 2024);

    expect($results['balances_updated'])->toBe(0);

    $newBalance = LeaveBalance::where('employee_id', $this->employee->id)
        ->where('leave_type_id', $this->leaveType->id)
        ->where('year', 2024)
        ->first();

    expect($newBalance)->toBeNull();
});