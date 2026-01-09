<?php

namespace Modules\Hostels\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Hostels\Models\HostelStaffAttendance;
use Modules\Hostels\Models\HostelStaffRoleAssignment;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\EmployeeSalary;

class PayrollSyncService
{
    /**
     * Sync hostel staff attendance to HR payroll system
     */
    public function syncAttendanceToPayroll(Carbon $startDate, Carbon $endDate, ?int $hostelId = null): array
    {
        $results = [
            'synced' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        // Get hostel staff attendance records for the period
        $query = HostelStaffAttendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->where('is_approved', true);

        if ($hostelId) {
            $query->where('hostel_id', $hostelId);
        }

        $attendanceRecords = $query->get();

        foreach ($attendanceRecords as $attendance) {
            try {
                // Check if already synced to HR
                $existingRecord = AttendanceRecord::where([
                    'employee_id' => $attendance->employee_id,
                    'date' => $attendance->attendance_date,
                ])->exists();

                if ($existingRecord) {
                    $results['skipped']++;

                    continue;
                }

                // Create HR attendance record
                AttendanceRecord::create([
                    'company_id' => $this->getCompanyIdFromHostel($attendance->hostel_id),
                    'employee_id' => $attendance->employee_id,
                    'date' => $attendance->attendance_date,
                    'status' => $this->mapAttendanceStatus($attendance->status),
                    'check_in_time' => $attendance->clock_in_time,
                    'check_out_time' => $attendance->clock_out_time,
                    'notes' => 'Synced from Hostel Staff Attendance: '.$attendance->notes,
                ]);

                $results['synced']++;

            } catch (\Exception $e) {
                $results['errors'][] = [
                    'employee_id' => $attendance->employee_id,
                    'date' => $attendance->attendance_date,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Calculate payroll for hostel staff based on attendance and roles
     */
    public function calculateHostelStaffPayroll(Carbon $startDate, Carbon $endDate, ?int $hostelId = null): array
    {
        $payrollData = [];

        // Get active staff assignments for the period
        $query = HostelStaffRoleAssignment::where(function ($q) use ($startDate, $endDate) {
            $q->where('start_date', '<=', $endDate)
                ->where(function ($q2) use ($startDate) {
                    $q2->where('end_date', '>=', $startDate)
                        ->orWhereNull('end_date');
                });
        })
            ->where('is_primary', true);

        if ($hostelId) {
            $query->where('hostel_id', $hostelId);
        }

        $assignments = $query->get();

        foreach ($assignments as $assignment) {
            try {
                // Get attendance records for this employee
                $attendanceRecords = HostelStaffAttendance::where([
                    'employee_id' => $assignment->employee_id,
                    'hostel_id' => $assignment->hostel_id,
                ])
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->where('is_approved', true)
                    ->get();

                // Calculate worked days and hours
                $workedDays = $attendanceRecords->count();
                $totalHours = $attendanceRecords->sum('hours_worked');

                // Get staff role salary information
                $role = $assignment->role;
                $dailyRate = $role->base_salary / 30; // Assuming monthly salary
                $hourlyRate = $dailyRate / 8; // Assuming 8-hour workday

                // Calculate earnings
                $basicSalary = $dailyRate * $workedDays;
                $overtimeHours = max(0, $totalHours - ($workedDays * 8));
                $overtimePay = $overtimeHours * ($hourlyRate * 1.5); // 1.5x for overtime

                $payrollData[] = [
                    'employee_id' => $assignment->employee_id,
                    'hostel_id' => $assignment->hostel_id,
                    'role_id' => $assignment->role_id,
                    'role_name' => $role->name,
                    'period_start' => $startDate->format('Y-m-d'),
                    'period_end' => $endDate->format('Y-m-d'),
                    'worked_days' => $workedDays,
                    'total_hours' => $totalHours,
                    'overtime_hours' => $overtimeHours,
                    'basic_salary' => round($basicSalary, 2),
                    'overtime_pay' => round($overtimePay, 2),
                    'total_earnings' => round($basicSalary + $overtimePay, 2),
                    'currency' => $role->salary_currency,
                ];

            } catch (\Exception $e) {
                // Log error but continue processing other assignments
                Log::error('Payroll calculation error for employee '.$assignment->employee_id.': '.$e->getMessage());
            }
        }

        return $payrollData;
    }

    /**
     * Export payroll data to HR system for processing
     */
    public function exportPayrollToHR(array $payrollData): bool
    {
        try {
            DB::beginTransaction();

            foreach ($payrollData as $data) {
                // Create or update employee salary record in HR system
                EmployeeSalary::updateOrCreate(
                    [
                        'employee_id' => $data['employee_id'],
                        'effective_from' => $data['period_start'],
                        'is_current' => true,
                    ],
                    [
                        'company_id' => $this->getCompanyIdFromHostel($data['hostel_id']),
                        'basic_salary' => $data['total_earnings'],
                        'currency' => $data['currency'],
                        'effective_to' => $data['period_end'],
                        'notes' => 'Hostel staff payroll: '.$data['role_name'].', Days: '.$data['worked_days'],
                    ]
                );
            }

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll export failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Map hostel attendance status to HR attendance status
     */
    protected function mapAttendanceStatus(string $hostelStatus): string
    {
        $statusMap = [
            'present' => 'present',
            'absent' => 'absent',
            'late' => 'late',
            'early_departure' => 'early_departure',
            'half_day' => 'half_day',
        ];

        return $statusMap[$hostelStatus] ?? 'present';
    }

    /**
     * Get company ID from hostel ID
     */
    protected function getCompanyIdFromHostel(int $hostelId): int
    {
        // Query the hostels table to get the company_id
        $hostel = \Modules\Hostels\Models\Hostel::find($hostelId);

        if (! $hostel) {
            throw new \Exception("Hostel with ID {$hostelId} not found");
        }

        if (! $hostel->company_id) {
            throw new \Exception("Hostel with ID {$hostelId} does not have a company assigned");
        }

        return $hostel->company_id;
    }
}
