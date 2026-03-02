<?php

use App\Models\Company;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;
use Modules\HR\Services\LeaveBalanceService;

beforeEach(function () {
    $this->service = app(LeaveBalanceService::class);
    $this->company = Company::factory()->create();

    $this->employee = Employee::factory()->create([
        'company_id'        => $this->company->id,
        'employment_status' => 'active',
    ]);

    $this->leaveType = LeaveType::factory()->create([
        'company_id'        => $this->company->id,
        'is_active'         => true,
        'max_days_per_year' => 20,
    ]);
});

test('it creates a leave balance when none exists', function () {
    $balance = $this->service->getOrCreateBalance($this->employee, $this->leaveType, now()->year);

    expect($balance)->toBeInstanceOf(LeaveBalance::class);
    expect((float) $balance->current_balance)->toBe(20.0);
    expect($balance->year)->toBe(now()->year);
});

test('it returns existing balance without creating a duplicate', function () {
    $first  = $this->service->getOrCreateBalance($this->employee, $this->leaveType, now()->year);
    $second = $this->service->getOrCreateBalance($this->employee, $this->leaveType, now()->year);

    expect($first->id)->toBe($second->id);
    expect(LeaveBalance::count())->toBe(1);
});

test('it initializes balances for all active leave types', function () {
    LeaveType::factory()->create([
        'company_id'        => $this->company->id,
        'is_active'         => true,
        'max_days_per_year' => 10,
    ]);

    $this->service->initializeEmployeeBalances($this->employee, now()->year);

    expect(LeaveBalance::where('employee_id', $this->employee->id)->count())->toBe(2);
});

test('it skips inactive leave types during initialization', function () {
    LeaveType::factory()->create([
        'company_id' => $this->company->id,
        'is_active'  => false,
    ]);

    $this->service->initializeEmployeeBalances($this->employee, now()->year);

    // Only the active one from beforeEach
    expect(LeaveBalance::where('employee_id', $this->employee->id)->count())->toBe(1);
});

test('it returns max_days_per_year as current balance for new balance', function () {
    $leaveType = LeaveType::factory()->create([
        'company_id'        => $this->company->id,
        'is_active'         => true,
        'max_days_per_year' => 15,
    ]);

    $balance = $this->service->getOrCreateBalance($this->employee, $leaveType, now()->year);

    expect((float) $balance->initial_balance)->toBe(15.0);
    expect((float) $balance->current_balance)->toBe(15.0);
});

test('it can get current balance for employee and leave type', function () {
    LeaveBalance::factory()->create([
        'company_id'      => $this->company->id,
        'employee_id'     => $this->employee->id,
        'leave_type_id'   => $this->leaveType->id,
        'year'            => now()->year,
        'current_balance' => 12.5,
    ]);

    $balance = $this->service->getCurrentBalance($this->employee, $this->leaveType, now()->year);

    expect((float) $balance)->toBe(12.5);
});

test('it returns max_days_per_year when no balance record exists', function () {
    $currentBalance = $this->service->getCurrentBalance($this->employee, $this->leaveType, now()->year);

    expect((float) $currentBalance)->toBe(20.0);
});
