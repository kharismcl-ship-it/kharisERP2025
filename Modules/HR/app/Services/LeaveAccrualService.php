<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;

class LeaveAccrualService
{
    /**
     * Process monthly leave accrual for all active employees.
     * Should be called once per month (e.g. first day of the month).
     */
    public function processMonthlyAccrual(?int $year = null, ?int $month = null): array
    {
        $year  = $year  ?? now()->year;
        $month = $month ?? now()->month;

        $results = [
            'employees_processed' => 0,
            'balances_updated'    => 0,
            'errors'              => [],
        ];

        try {
            DB::beginTransaction();

            $employees = Employee::where('employment_status', 'active')->get();
            $accrualDate = Carbon::create($year, $month, 1);

            foreach ($employees as $employee) {
                $leaveTypes = LeaveType::where('company_id', $employee->company_id)
                    ->where('is_active', true)
                    ->where('has_accrual', true)
                    ->whereIn('accrual_frequency', ['monthly'])
                    ->get();

                foreach ($leaveTypes as $leaveType) {
                    try {
                        $accrualAmount = $this->calculateProRataAccrual($employee, $leaveType, $accrualDate);

                        if ($accrualAmount <= 0) {
                            continue;
                        }

                        $balance = LeaveBalance::firstOrCreate(
                            [
                                'employee_id'   => $employee->id,
                                'leave_type_id' => $leaveType->id,
                                'year'          => $year,
                            ],
                            [
                                'company_id'      => $employee->company_id,
                                'initial_balance' => $leaveType->max_days_per_year ?? 0,
                                'current_balance' => $leaveType->max_days_per_year ?? 0,
                                'carried_over'    => 0,
                                'adjustments'     => 0,
                                'used_balance'    => 0,
                            ]
                        );

                        $balance->adjustments = round(($balance->adjustments ?? 0) + $accrualAmount, 2);
                        $balance->calculateCurrentBalance();
                        $balance->save();

                        $results['balances_updated']++;

                        Log::info('Monthly leave accrual processed', [
                            'employee_id'   => $employee->id,
                            'leave_type_id' => $leaveType->id,
                            'year'          => $year,
                            'month'         => $month,
                            'accrual'       => $accrualAmount,
                        ]);
                    } catch (\Exception $e) {
                        $results['errors'][] = "Employee {$employee->id} / LeaveType {$leaveType->id}: " . $e->getMessage();
                    }
                }

                $results['employees_processed']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = 'Monthly accrual failed: ' . $e->getMessage();
            Log::error('Monthly leave accrual failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Process year-end carry-over for all employees
     */
    public function processYearEndCarryOver(int $fromYear, int $toYear): array
    {
        $results = [
            'employees_processed' => 0,
            'balances_updated' => 0,
            'errors' => [],
        ];

        try {
            DB::beginTransaction();

            // Get all active employees
            $employees = Employee::where('employment_status', 'active')->get();

            foreach ($employees as $employee) {
                $result = $this->processEmployeeCarryOver($employee, $fromYear, $toYear);
                $results['employees_processed']++;
                $results['balances_updated'] += $result['balances_updated'];

                if (! empty($result['errors'])) {
                    $results['errors'] = array_merge($results['errors'], $result['errors']);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = 'Year-end processing failed: '.$e->getMessage();
            Log::error('Year-end carry-over processing failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Process carry-over for a single employee
     */
    protected function processEmployeeCarryOver(Employee $employee, int $fromYear, int $toYear): array
    {
        $result = ['balances_updated' => 0, 'errors' => []];

        try {
            // Get all leave types with accrual enabled for the employee's company
            $leaveTypes = LeaveType::where('company_id', $employee->company_id)
                ->where('is_active', true)
                ->where('has_accrual', true)
                ->get();

            foreach ($leaveTypes as $leaveType) {
                // Get the previous year's balance
                $previousBalance = LeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $fromYear)
                    ->first();

                if (! $previousBalance) {
                    continue; // No balance to carry over
                }

                // Calculate carry-over amount based on leave type rules
                $carryOverAmount = $this->calculateCarryOverAmount($previousBalance, $leaveType);

                if ($carryOverAmount > 0) {
                    // Create or update the new year's balance
                    $newBalance = LeaveBalance::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $toYear,
                        ],
                        [
                            'company_id' => $employee->company_id,
                            'initial_balance' => 0,
                            'current_balance' => 0,
                            'carried_over' => 0,
                            'adjustments' => 0,
                            'used_balance' => 0,
                        ]
                    );

                    // Apply the carry-over
                    $newBalance->carried_over = $carryOverAmount;
                    $newBalance->adjustments += $carryOverAmount;
                    $newBalance->calculateCurrentBalance();
                    $newBalance->save();

                    $result['balances_updated']++;

                    // Log the carry-over operation
                    Log::info('Leave carry-over processed', [
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'from_year' => $fromYear,
                        'to_year' => $toYear,
                        'carry_over_amount' => $carryOverAmount,
                        'previous_balance' => $previousBalance->current_balance,
                    ]);
                }
            }

        } catch (\Exception $e) {
            $result['errors'][] = "Employee {$employee->id}: ".$e->getMessage();
        }

        return $result;
    }

    /**
     * Calculate carry-over amount based on leave type rules
     */
    protected function calculateCarryOverAmount(LeaveBalance $balance, LeaveType $leaveType): float
    {
        $currentBalance = $balance->current_balance;
        $carryOverLimit = $leaveType->carryover_limit ?? 0;
        $maxBalance = $leaveType->max_balance;

        // If no carry-over is allowed or balance is zero
        if ($carryOverLimit <= 0 || $currentBalance <= 0) {
            return 0;
        }

        // Apply carry-over limit
        $carryOverAmount = min($currentBalance, $carryOverLimit);

        // Check if carry-over would exceed maximum balance cap
        if ($maxBalance > 0) {
            // Get the new year's initial balance (without carry-over)
            $initialBalance = $leaveType->max_days_per_year;
            $projectedBalance = $initialBalance + $carryOverAmount;

            if ($projectedBalance > $maxBalance) {
                $carryOverAmount = max(0, $maxBalance - $initialBalance);
            }
        }

        return round($carryOverAmount, 2);
    }

    /**
     * Calculate pro-rata accrual for new hires
     */
    public function calculateProRataAccrual(Employee $employee, LeaveType $leaveType, Carbon $accrualDate): float
    {
        if (! $leaveType->pro_rata_enabled || ! $employee->joining_date) {
            return $leaveType->accrual_rate;
        }

        $joiningDate = $employee->joining_date;

        // If joining date is after accrual date, return 0
        if ($joiningDate > $accrualDate) {
            return 0;
        }

        // Calculate based on accrual frequency
        switch ($leaveType->accrual_frequency) {
            case 'monthly':
                return $this->calculateMonthlyProRata($leaveType->accrual_rate, $joiningDate, $accrualDate);

            case 'quarterly':
                return $this->calculateQuarterlyProRata($leaveType->accrual_rate, $joiningDate, $accrualDate);

            case 'annually':
                return $this->calculateAnnualProRata($leaveType->accrual_rate, $joiningDate, $accrualDate);

            default:
                return $leaveType->accrual_rate;
        }
    }

    protected function calculateMonthlyProRata(float $baseRate, Carbon $joiningDate, Carbon $accrualDate): float
    {
        $monthStart = $accrualDate->copy()->startOfMonth();
        $monthEnd = $accrualDate->copy()->endOfMonth();

        $daysInMonth = $monthStart->daysInMonth;
        $daysWorked = $daysInMonth;

        // Adjust for joining during the month
        if ($joiningDate > $monthStart) {
            $daysWorked = $joiningDate->diffInDays($monthEnd) + 1;
        }

        return $baseRate * ($daysWorked / $daysInMonth);
    }

    protected function calculateQuarterlyProRata(float $baseRate, Carbon $joiningDate, Carbon $accrualDate): float
    {
        $quarterStart = $accrualDate->copy()->firstOfQuarter();
        $quarterEnd = $accrualDate->copy()->lastOfQuarter();

        $daysInQuarter = $quarterStart->diffInDays($quarterEnd) + 1;
        $daysWorked = $daysInQuarter;

        // Adjust for joining during the quarter
        if ($joiningDate > $quarterStart) {
            $daysWorked = $joiningDate->diffInDays($quarterEnd) + 1;
        }

        return $baseRate * ($daysWorked / $daysInQuarter);
    }

    protected function calculateAnnualProRata(float $baseRate, Carbon $joiningDate, Carbon $accrualDate): float
    {
        $yearStart = $accrualDate->copy()->startOfYear();
        $yearEnd = $accrualDate->copy()->endOfYear();

        $daysInYear = $yearStart->isLeapYear() ? 366 : 365;
        $daysWorked = $daysInYear;

        // Adjust for joining during the year
        if ($joiningDate > $yearStart) {
            $daysWorked = $joiningDate->diffInDays($yearEnd) + 1;
        }

        return $baseRate * ($daysWorked / $daysInYear);
    }

    /**
     * Get accrual forecast for an employee
     */
    public function getAccrualForecast(Employee $employee, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $forecast = [];

        $leaveTypes = LeaveType::where('company_id', $employee->company_id)
            ->where('is_active', true)
            ->where('has_accrual', true)
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $balance = LeaveBalance::findOrCreateForEmployee($employee->id, $leaveType->id, $year);

            $forecast[] = [
                'leave_type' => $leaveType->name,
                'current_balance' => $balance->current_balance,
                'accrual_rate' => $leaveType->accrual_rate,
                'accrual_frequency' => $leaveType->accrual_frequency,
                'projected_year_end' => $this->calculateProjectedYearEndBalance($balance, $leaveType),
                'carryover_limit' => $leaveType->carryover_limit,
                'max_balance' => $leaveType->max_balance,
            ];
        }

        return $forecast;
    }

    protected function calculateProjectedYearEndBalance(LeaveBalance $balance, LeaveType $leaveType): float
    {
        $currentBalance = $balance->current_balance;
        $remainingAccruals = $this->getRemainingAccruals($leaveType->accrual_frequency);

        $projectedAccruals = $leaveType->accrual_rate * $remainingAccruals;
        $projectedBalance = $currentBalance + $projectedAccruals;

        // Apply maximum balance cap if set
        if ($leaveType->max_balance > 0) {
            $projectedBalance = min($projectedBalance, $leaveType->max_balance);
        }

        return round($projectedBalance, 2);
    }

    protected function getRemainingAccruals(string $frequency): int
    {
        $currentMonth = now()->month;

        switch ($frequency) {
            case 'monthly':
                return 12 - $currentMonth + 1;

            case 'quarterly':
                $currentQuarter = ceil($currentMonth / 3);

                return 4 - $currentQuarter + 1;

            case 'annually':
                return 1; // Only one annual accrual remaining

            default:
                return 0;
        }
    }
}
