<?php

namespace Modules\Hostels\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelHousekeepingSchedule;
use Modules\Hostels\Models\Room;
use Modules\HR\Models\Employee;

class HostelHousekeepingScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        // Get rooms for cleaning
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->call(RoomSeeder::class);
            $rooms = Room::all();
        }

        // Get housekeeping staff
        $housekeepingStaff = Employee::whereHas('hostelAssignments', function ($query) {
            $query->whereHas('role', function ($roleQuery) {
                $roleQuery->where('slug', 'like', '%housekeeper%')
                    ->orWhere('name', 'like', '%housekeeping%');
            });
        })->get();

        // If no housekeeping staff, get any available employees
        if ($housekeepingStaff->isEmpty()) {
            $housekeepingStaff = Employee::whereHas('hostelAssignments')->limit(5)->get();
        }

        if ($housekeepingStaff->isEmpty()) {
            // Create some basic staff if none exist
            $this->call(HostelStaffRoleSeeder::class);
            $housekeepingStaff = Employee::whereHas('hostelAssignments')->limit(5)->get();
        }

        $cleaningTypes = ['daily', 'deep', 'weekly', 'monthly', 'checkout'];
        $cleaningWeights = [40, 15, 25, 10, 10]; // Weighted probabilities
        $statuses = ['pending', 'in_progress', 'completed'];
        $statusWeights = [30, 20, 50]; // More completed than pending/in-progress

        // Generate schedules for the next 30 days
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(30);

        foreach ($hostels as $hostel) {
            $hostelRooms = $rooms->where('hostel_id', $hostel->id);
            $hostelStaff = $housekeepingStaff->filter(function ($employee) use ($hostel) {
                return $employee->hostelAssignments()->where('hostel_id', $hostel->id)->exists();
            });

            if ($hostelRooms->isEmpty() || $hostelStaff->isEmpty()) {
                continue;
            }

            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // Skip weekends for most cleaning types
                if ($currentDate->isWeekend() && rand(1, 10) > 2) { // 20% chance of weekend cleaning
                    $currentDate->addDay();

                    continue;
                }

                // Determine how many rooms to clean today (1-5 rooms per day)
                $roomsToClean = $hostelRooms->random(min(5, $hostelRooms->count()));

                foreach ($roomsToClean as $room) {
                    $cleaningType = $this->getWeightedRandomType($cleaningTypes, $cleaningWeights);
                    $status = $this->getWeightedRandomStatus($statuses, $statusWeights);
                    $assignedStaff = $hostelStaff->random();

                    $scheduleData = [
                        'hostel_id' => $hostel->id,
                        'room_id' => $room->id,
                        'assigned_employee_id' => $assignedStaff->id,
                        'schedule_date' => $currentDate->toDateString(),
                        'cleaning_type' => $cleaningType,
                        'status' => $status,
                        'quality_score' => null,
                        'notes' => $this->generateCleaningNotes($cleaningType),
                    ];

                    // Add timestamps for in-progress and completed statuses
                    if ($status === 'in_progress') {
                        $scheduleData['started_at'] = $currentDate->copy()->setTime(rand(8, 10), rand(0, 59));
                    } elseif ($status === 'completed') {
                        $scheduleData['started_at'] = $currentDate->copy()->setTime(rand(8, 10), rand(0, 59));
                        $scheduleData['completed_at'] = $scheduleData['started_at']->copy()->addHours(rand(1, 3));
                        $scheduleData['quality_score'] = rand(80, 100); // Quality score 80-100%
                    }

                    HostelHousekeepingSchedule::create($scheduleData);
                }

                $currentDate->addDay();
            }
        }
    }

    private function getWeightedRandomType(array $types, array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $index => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $types[$index];
            }
        }

        return $types[0]; // Default to first type
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

    private function generateCleaningNotes(string $cleaningType): string
    {
        return match ($cleaningType) {
            'daily' => 'Routine daily cleaning - bed making, trash removal, surface wiping',
            'deep' => 'Deep cleaning - carpet shampooing, window cleaning, thorough disinfection',
            'weekly' => 'Weekly comprehensive cleaning - all surfaces, bathroom deep clean',
            'monthly' => 'Monthly maintenance cleaning - equipment check, deep sanitation',
            'checkout' => 'Check-out cleaning - prepare room for new tenant, full reset',
            default => 'Standard cleaning procedure'
        };
    }
}
