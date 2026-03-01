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
        if (HostelHousekeepingSchedule::exists()) {
            return;
        }

        $hostels = Hostel::all();
        if ($hostels->isEmpty()) {
            return;
        }

        // Use any available employees (limit to avoid OOM)
        $staff = Employee::limit(5)->get();
        if ($staff->isEmpty()) {
            return;
        }

        $cleaningTypes = ['daily', 'deep', 'weekly', 'monthly', 'checkout'];
        $statuses     = ['pending', 'in_progress', 'completed'];
        $startDate    = Carbon::now();

        foreach ($hostels as $hostel) {
            // Load only a small sample of rooms per hostel to avoid OOM
            $rooms = Room::where('hostel_id', $hostel->id)->limit(10)->get();
            if ($rooms->isEmpty()) {
                continue;
            }

            // Generate 7 days of schedules
            for ($day = 0; $day < 7; $day++) {
                $currentDate = $startDate->copy()->addDays($day);

                foreach ($rooms->random(min(3, $rooms->count())) as $room) {
                    $status       = $statuses[array_rand($statuses)];
                    $cleaningType = $cleaningTypes[array_rand($cleaningTypes)];
                    $scheduleData = [
                        'hostel_id'            => $hostel->id,
                        'room_id'              => $room->id,
                        'assigned_employee_id' => $staff->random()->id,
                        'schedule_date'        => $currentDate->toDateString(),
                        'cleaning_type'        => $cleaningType,
                        'status'               => $status,
                        'notes'                => $this->generateCleaningNotes($cleaningType),
                    ];

                    if ($status === 'in_progress') {
                        $scheduleData['started_at'] = $currentDate->copy()->setTime(8, 0);
                    } elseif ($status === 'completed') {
                        $scheduleData['started_at']   = $currentDate->copy()->setTime(8, 0);
                        $scheduleData['completed_at'] = $currentDate->copy()->setTime(11, 0);
                        $scheduleData['quality_score'] = rand(80, 100);
                    }

                    HostelHousekeepingSchedule::create($scheduleData);
                }
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
