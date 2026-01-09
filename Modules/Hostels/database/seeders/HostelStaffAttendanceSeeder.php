<?php

namespace Modules\Hostels\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelStaffAttendance;
use Modules\HR\Models\Employee;

class HostelStaffAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        // Get employees assigned to hostels
        $employees = Employee::whereHas('hostelAssignments')->get();

        if ($employees->isEmpty()) {
            $this->call(HostelStaffRoleSeeder::class);
            $employees = Employee::whereHas('hostelAssignments')->get();

            // If still empty, create some basic assignments
            if ($employees->isEmpty()) {
                $this->createBasicStaffAssignments($hostels);
                $employees = Employee::whereHas('hostelAssignments')->get();
            }
        }

        // Generate attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $attendanceStatuses = ['present', 'absent', 'late', 'early_departure', 'half_day'];
        $attendanceWeights = [70, 10, 8, 7, 5]; // Weighted probabilities

        foreach ($hostels as $hostel) {
            $hostelEmployees = $employees->filter(function ($employee) use ($hostel) {
                return $employee->hostelAssignments()->where('hostel_id', $hostel->id)->exists();
            });

            if ($hostelEmployees->isEmpty()) {
                continue;
            }

            foreach ($hostelEmployees as $employee) {
                $currentDate = $startDate->copy();

                while ($currentDate <= $endDate) {
                    // Skip weekends (Saturday and Sunday)
                    if ($currentDate->isWeekend()) {
                        $currentDate->addDay();

                        continue;
                    }

                    $status = $this->getWeightedRandomStatus($attendanceStatuses, $attendanceWeights);

                    $attendanceData = [
                        'tenant_id' => $hostel->tenant_id,
                        'hostel_id' => $hostel->id,
                        'employee_id' => $employee->id,
                        'attendance_date' => $currentDate->toDateString(),
                        'status' => $status,
                        'is_approved' => true,
                        'approved_by' => 1, // Assuming admin user ID 1
                        'approved_at' => $currentDate->copy()->addHours(rand(1, 4)),
                    ];

                    // Add clock times for present/late/half_day statuses
                    if (in_array($status, ['present', 'late', 'half_day', 'early_departure'])) {
                        $clockIn = $this->generateClockInTime($status, $currentDate);
                        $clockOut = $this->generateClockOutTime($status, $clockIn);

                        $attendanceData['clock_in_time'] = $clockIn->format('H:i:s');
                        $attendanceData['clock_out_time'] = $clockOut->format('H:i:s');
                        $attendanceData['hours_worked'] = $clockOut->diffInHours($clockIn, true);
                    }

                    // Add notes for certain statuses
                    if ($status === 'late') {
                        $attendanceData['notes'] = 'Arrived late due to traffic';
                    } elseif ($status === 'early_departure') {
                        $attendanceData['notes'] = 'Left early for personal reasons';
                    } elseif ($status === 'half_day') {
                        $attendanceData['notes'] = 'Worked half day - medical appointment';
                    } elseif ($status === 'absent') {
                        $attendanceData['notes'] = 'Sick leave';
                    }

                    HostelStaffAttendance::create($attendanceData);

                    $currentDate->addDay();
                }
            }
        }
    }

    private function getWeightedRandomStatus(array $statuses, array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $index => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $statuses[$index];
            }
        }

        return $statuses[0]; // Default to first status
    }

    private function generateClockInTime(string $status, Carbon $date): Carbon
    {
        $baseTime = $date->copy()->setTime(8, 0, 0); // Normal start: 8:00 AM

        return match ($status) {
            'late' => $baseTime->addMinutes(rand(15, 120)), // 15 mins to 2 hours late
            'half_day' => $baseTime->addHours(rand(2, 4)), // Come in late for half day
            'early_departure' => $baseTime->subMinutes(rand(0, 30)), // Slightly early
            default => $baseTime->addMinutes(rand(-15, 15)) // ±15 minutes variation
        };
    }

    private function generateClockOutTime(string $status, Carbon $clockIn): Carbon
    {
        $normalEnd = $clockIn->copy()->addHours(8); // 8-hour workday

        return match ($status) {
            'early_departure' => $normalEnd->subHours(rand(2, 4)), // Leave 2-4 hours early
            'half_day' => $clockIn->copy()->addHours(rand(3, 5)), // Work 3-5 hours
            default => $normalEnd->addMinutes(rand(-30, 60)) // Normal variation
        };
    }

    private function createBasicStaffAssignments($hostels): void
    {
        // Get some employees to assign to hostels
        $employees = Employee::limit(10)->get();

        if ($employees->isEmpty()) {
            // Create some basic employees if none exist
            $employees = Employee::factory()->count(5)->create();
        }

        foreach ($hostels as $hostel) {
            foreach ($employees as $employee) {
                // Create a basic staff assignment
                \Modules\Hostels\Models\HostelStaffRoleAssignment::create([
                    'tenant_id' => $hostel->tenant_id,
                    'hostel_id' => $hostel->id,
                    'employee_id' => $employee->id,
                    'role_id' => \Modules\Hostels\Models\HostelStaffRole::firstOrCreate([
                        'tenant_id' => $hostel->tenant_id,
                        'name' => 'General Staff',
                        'slug' => 'general-staff',
                        'description' => 'General hostel staff member',
                        'permissions' => ['view_basic_info'],
                        'base_salary' => 150000, // 1,500 GHS
                        'salary_currency' => 'GHS',
                        'is_active' => true,
                    ])->id,
                    'start_date' => now()->subMonths(3),
                    'is_primary' => true,
                    'assignment_reason' => 'Initial staff assignment',
                ]);
            }
        }
    }
}
