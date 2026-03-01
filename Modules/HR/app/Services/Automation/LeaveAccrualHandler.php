<?php

namespace Modules\HR\Services\Automation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\AutomationSetting;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;

class LeaveAccrualHandler
{
    public function execute(AutomationSetting $setting): array
    {
        $companyId = $setting->company_id;
        $config = $setting->config ?? [];

        $this->info("Starting leave accrual automation for company {$companyId}");

        $processed = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            // Get all active employees for the company
            $employees = Employee::where('company_id', $companyId)
                ->where('employment_status', 'active')
                ->with(['leaveBalances.leaveType'])
                ->get();

            foreach ($employees as $employee) {
                $result = $this->processEmployeeLeaveAccrual($employee, $config);
                $processed += $result['processed'];

                if (! empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
            }

            DB::commit();

            return [
                'success' => empty($errors),
                'records_processed' => $processed,
                'details' => [
                    'employees_processed' => $employees->count(),
                    'errors' => $errors,
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Leave accrual automation failed for company {$companyId}", ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'records_processed' => $processed,
                'details' => ['errors' => $errors],
            ];
        }
    }

    protected function processEmployeeLeaveAccrual(Employee $employee, array $config): array
    {
        $processed = 0;
        $errors = [];

        // Get all active leave types with accrual enabled
        $leaveTypes = LeaveType::where('company_id', $employee->company_id)
            ->where('is_active', true)
            ->where('has_accrual', true)
            ->get();

        foreach ($leaveTypes as $leaveType) {
            try {
                $balance = LeaveBalance::findOrCreateForEmployee(
                    $employee->id,
                    $leaveType->id,
                    now()->year
                );

                $accrualRate = $this->calculateAccrualRate($leaveType, $employee, $config);

                if ($accrualRate > 0) {
                    $balance->accrueLeave(
                        $accrualRate,
                        'Automated accrual for '.now()->format('F Y')
                    );
                    $balance->save();
                    $processed++;
                }

            } catch (\Exception $e) {
                $errors[] = "Employee {$employee->id}, LeaveType {$leaveType->id}: {$e->getMessage()}";
                Log::error("Leave accrual failed for employee {$employee->id}, leave type {$leaveType->id}", ['error' => $e->getMessage()]);
            }
        }

        return [
            'processed' => $processed,
            'errors' => $errors,
        ];
    }

    protected function calculateAccrualRate(LeaveType $leaveType, Employee $employee, array $config): float
    {
        // Use the configured accrual rate instead of calculating from max_days_per_year
        $baseRate = $leaveType->accrual_rate;

        // Handle different accrual frequencies
        $baseRate = $this->adjustForFrequency($baseRate, $leaveType->accrual_frequency);

        // Apply employment type adjustments
        $baseRate = $this->applyEmploymentTypeAdjustment($baseRate, $employee);

        // Apply pro-rata calculations for new hires (if enabled)
        if ($leaveType->pro_rata_enabled && $employee->joining_date) {
            $baseRate = $this->calculateProRataRate($baseRate, $employee, $leaveType->accrual_frequency);
        }

        // Apply configuration overrides
        if (isset($config['accrual_multiplier'])) {
            $baseRate = $baseRate * $config['accrual_multiplier'];
        }

        return round($baseRate, 2);
    }

    protected function calculateProRataRate(float $baseRate, Employee $employee, string $frequency): float
    {
        $joiningDate = $employee->joining_date;
        $currentDate = now();

        // If joining date is in the future, return 0
        if ($joiningDate > $currentDate) {
            return 0;
        }

        // If employee has termination date and it's in the past, return 0
        if ($employee->termination_date && $employee->termination_date < $currentDate) {
            return 0;
        }

        // Calculate pro-rata based on frequency and current accrual period
        switch ($frequency) {
            case 'monthly':
                return $this->calculateMonthlyProRata($baseRate, $joiningDate, $employee->termination_date);

            case 'quarterly':
                return $this->calculateQuarterlyProRata($baseRate, $joiningDate, $employee->termination_date);

            case 'annually':
                return $this->calculateAnnualProRata($baseRate, $joiningDate, $employee->termination_date);

            default:
                return $baseRate;
        }
    }

    protected function calculateMonthlyProRata(float $baseRate, \Carbon\Carbon $joiningDate, ?\Carbon\Carbon $terminationDate): float
    {
        $currentMonth = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // If joining date is after current month, return 0
        if ($joiningDate > $monthEnd) {
            return 0;
        }

        // If termination date is before current month, return 0
        if ($terminationDate && $terminationDate < $currentMonth) {
            return 0;
        }

        $daysInMonth = $currentMonth->daysInMonth;
        $daysWorked = $daysInMonth;

        // Adjust for joining during the month
        if ($joiningDate > $currentMonth) {
            $daysWorked = $joiningDate->diffInDays($monthEnd) + 1;
        }

        // Adjust for termination during the month
        if ($terminationDate && $terminationDate < $monthEnd) {
            $daysWorked = $currentMonth->diffInDays($terminationDate) + 1;
        }

        return $baseRate * ($daysWorked / $daysInMonth);
    }

    protected function calculateQuarterlyProRata(float $baseRate, \Carbon\Carbon $joiningDate, ?\Carbon\Carbon $terminationDate): float
    {
        $currentQuarter = now()->firstOfQuarter();
        $quarterEnd = now()->lastOfQuarter();

        // If joining date is after current quarter, return 0
        if ($joiningDate > $quarterEnd) {
            return 0;
        }

        // If termination date is before current quarter, return 0
        if ($terminationDate && $terminationDate < $currentQuarter) {
            return 0;
        }

        $daysInQuarter = $currentQuarter->diffInDays($quarterEnd) + 1;
        $daysWorked = $daysInQuarter;

        // Adjust for joining during the quarter
        if ($joiningDate > $currentQuarter) {
            $daysWorked = $joiningDate->diffInDays($quarterEnd) + 1;
        }

        // Adjust for termination during the quarter
        if ($terminationDate && $terminationDate < $quarterEnd) {
            $daysWorked = $currentQuarter->diffInDays($terminationDate) + 1;
        }

        return $baseRate * ($daysWorked / $daysInQuarter);
    }

    protected function calculateAnnualProRata(float $baseRate, \Carbon\Carbon $joiningDate, ?\Carbon\Carbon $terminationDate): float
    {
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        // If joining date is after current year, return 0
        if ($joiningDate > $yearEnd) {
            return 0;
        }

        // If termination date is before current year, return 0
        if ($terminationDate && $terminationDate < $yearStart) {
            return 0;
        }

        $daysInYear = $yearStart->isLeapYear() ? 366 : 365;
        $daysWorked = $daysInYear;

        // Adjust for joining during the year
        if ($joiningDate > $yearStart) {
            $daysWorked = $joiningDate->diffInDays($yearEnd) + 1;
        }

        // Adjust for termination during the year
        if ($terminationDate && $terminationDate < $yearEnd) {
            $daysWorked = $yearStart->diffInDays($terminationDate) + 1;
        }

        return $baseRate * ($daysWorked / $daysInYear);
    }

    protected function applyEmploymentTypeAdjustment(float $baseRate, Employee $employee): float
    {
        $adjustmentFactor = 1.0;

        switch ($employee->employment_type) {
            case 'part_time':
                // For part-time employees, adjust based on standard hours (default 0.5)
                $adjustmentFactor = 0.5;

                // If employee has specific working hours, calculate precise adjustment
                if ($employee->standard_hours_per_week) {
                    $adjustmentFactor = $employee->standard_hours_per_week / 40; // 40 = full-time standard
                }
                break;

            case 'contract':
                // Contract employees typically don't accrue leave, but can be configured
                $adjustmentFactor = 0.0;
                break;

            case 'probation':
                // Probationary employees might have reduced accrual
                $adjustmentFactor = 0.8;
                break;

            case 'intern':
                // Interns typically don't accrue leave
                $adjustmentFactor = 0.0;
                break;

            default:
                // Full-time employees get full accrual
                $adjustmentFactor = 1.0;
                break;
        }

        return $baseRate * $adjustmentFactor;
    }

    protected function adjustForFrequency(float $rate, string $frequency): float
    {
        switch ($frequency) {
            case 'monthly':
                return $rate; // Already monthly rate
            case 'quarterly':
                return $rate / 3; // Convert quarterly to monthly equivalent
            case 'annually':
                return $rate / 12; // Convert annual to monthly equivalent
            default:
                return $rate; // Default to monthly
        }
    }

    protected function info(string $message): void
    {
        Log::info($message);
    }
}
