<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\LeaveType;

class LeaveBalanceService
{
    /**
     * Initialize leave balances for an employee for the current year.
     */
    public function initializeEmployeeBalances(Employee $employee, ?int $year = null): void
    {
        $year = $year ?? now()->year;

        $leaveTypes = LeaveType::where('company_id', $employee->company_id)
            ->where('is_active', true)
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $this->getOrCreateBalance($employee, $leaveType, $year);
        }
    }

    /**
     * Get or create leave balance for an employee and leave type.
     */
    public function getOrCreateBalance(Employee $employee, LeaveType $leaveType, int $year): LeaveBalance
    {
        return LeaveBalance::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
            ],
            [
                'company_id' => $employee->company_id,
                'initial_balance' => $leaveType->max_days_per_year,
                'current_balance' => $leaveType->max_days_per_year,
            ]
        );
    }

    /**
     * Update leave balances when a leave request is approved.
     */
    public function updateBalancesForApprovedRequest(LeaveRequest $leaveRequest): void
    {
        $balance = $this->getOrCreateBalance(
            $leaveRequest->employee,
            $leaveRequest->leaveType,
            $leaveRequest->start_date->year
        );

        if ($balance->isSufficient($leaveRequest->total_days)) {
            $balance->useDays($leaveRequest->total_days);
            $balance->save();
        } else {
            throw new \Exception('Insufficient leave balance');
        }
    }

    /**
     * Restore leave balances when a leave request is rejected or cancelled.
     */
    public function restoreBalancesForCancelledRequest(LeaveRequest $leaveRequest): void
    {
        $balance = $this->getOrCreateBalance(
            $leaveRequest->employee,
            $leaveRequest->leaveType,
            $leaveRequest->start_date->year
        );

        $balance->used_balance -= $leaveRequest->total_days;
        $balance->calculateCurrentBalance();
        $balance->save();
    }

    /**
     * Get current balance for an employee and leave type.
     */
    public function getCurrentBalance(Employee $employee, LeaveType $leaveType, ?int $year = null): float
    {
        $year = $year ?? now()->year;

        $balance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $year)
            ->first();

        return $balance ? $balance->current_balance : $leaveType->max_days_per_year;
    }

    /**
     * Get balance summary for an employee.
     */
    public function getEmployeeBalanceSummary(Employee $employee, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        return LeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', $year)
            ->get()
            ->mapWithKeys(function ($balance) {
                return [$balance->leaveType->name => [
                    'initial' => $balance->initial_balance,
                    'used' => $balance->used_balance,
                    'current' => $balance->current_balance,
                    'carried_over' => $balance->carried_over,
                    'adjustments' => $balance->adjustments,
                ]];
            })
            ->toArray();
    }

    /**
     * Process end-of-year carry over for all employees.
     */
    public function processYearEndCarryOver(int $fromYear, int $toYear, float $maxCarryOverPercentage = 50.0): void
    {
        DB::transaction(function () use ($fromYear, $toYear, $maxCarryOverPercentage) {
            $balances = LeaveBalance::where('year', $fromYear)->get();

            foreach ($balances as $balance) {
                if ($balance->current_balance > 0) {
                    // Calculate maximum allowed carry over based on percentage
                    $maxCarryOver = ($balance->leaveType->max_days_per_year * $maxCarryOverPercentage) / 100;
                    $carryOverDays = min($balance->current_balance, $maxCarryOver);

                    if ($carryOverDays > 0) {
                        // Create or update balance for next year
                        $nextYearBalance = $this->getOrCreateBalance(
                            $balance->employee,
                            $balance->leaveType,
                            $toYear
                        );

                        // Add carried over days to next year's balance
                        $nextYearBalance->carried_over += $carryOverDays;
                        $nextYearBalance->calculateCurrentBalance();
                        $nextYearBalance->save();

                        // Update current year balance with carry over note
                        $balance->notes = $balance->notes ? $balance->notes."\nCarried over {$carryOverDays} days to {$toYear}" : "Carried over {$carryOverDays} days to {$toYear}";
                        $balance->save();
                    }
                }
            }
        });
    }

    /**
     * Add manual adjustment to an employee's leave balance.
     */
    public function addManualAdjustment(Employee $employee, LeaveType $leaveType, float $days, string $reason, ?int $year = null): LeaveBalance
    {
        $year = $year ?? now()->year;

        $balance = $this->getOrCreateBalance($employee, $leaveType, $year);

        $balance->addAdjustment($days, $reason);
        $balance->save();

        return $balance;
    }
}
