<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Models\EmploymentContract;

class HRService
{
    /**
     * Calculate the number of leave days between two dates, excluding weekends.
     */
    public function calculateLeaveDays($startDate, $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $days = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $days++;
            }
            $start->addDay();
        }

        return $days;
    }

    /**
     * Get employees on leave for a specific date.
     */
    public function getEmployeesOnLeave($date, $companyId)
    {
        return Employee::where('company_id', $companyId)
            ->whereHas('leaveRequests', function ($query) use ($date) {
                $query->where('status', 'approved')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
            })
            ->with(['leaveRequests' => function ($query) use ($date) {
                $query->where('status', 'approved')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
            }])
            ->get();
    }

    /**
     * Get attendance summary for an employee.
     */
    public function getEmployeeAttendanceSummary($employeeId, $startDate, $endDate)
    {
        $records = AttendanceRecord::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_days' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'leave' => $records->where('status', 'leave')->count(),
            'remote' => $records->where('status', 'remote')->count(),
            'off' => $records->where('status', 'off')->count(),
        ];

        return $summary;
    }

    /**
     * Create a new employment contract for an employee.
     */
    public function createEmploymentContract($employeeId, $data)
    {
        $employee = Employee::findOrFail($employeeId);

        // Mark previous contracts as not current
        EmploymentContract::where('employee_id', $employeeId)
            ->update(['is_current' => false]);

        // Create new contract
        $contract = EmploymentContract::create(array_merge($data, [
            'employee_id' => $employeeId,
            'company_id' => $employee->company_id,
            'is_current' => true,
        ]));

        return $contract;
    }

    /**
     * Update employee salary.
     */
    public function updateEmployeeSalary($employeeId, $data)
    {
        $employee = Employee::findOrFail($employeeId);

        // Mark previous salaries as not current
        EmployeeSalary::where('employee_id', $employeeId)
            ->update(['is_current' => false]);

        // Create new salary record
        $salary = EmployeeSalary::create(array_merge($data, [
            'employee_id' => $employeeId,
            'company_id' => $employee->company_id,
            'is_current' => true,
        ]));

        return $salary;
    }
}
