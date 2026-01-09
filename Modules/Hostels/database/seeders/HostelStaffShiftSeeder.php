<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelStaffShift;

class HostelStaffShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $shiftTypes = ['morning', 'afternoon', 'night', 'general'];
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($hostels as $hostel) {
            foreach ($shiftTypes as $shiftType) {
                foreach ($daysOfWeek as $day) {
                    HostelStaffShift::create([
                        'hostel_id' => $hostel->id,
                        'name' => ucfirst($shiftType).' Shift - '.ucfirst($day),
                        'shift_type' => $shiftType,
                        'day_of_week' => $day,
                        'start_time' => $this->getStartTime($shiftType),
                        'end_time' => $this->getEndTime($shiftType),
                        'is_active' => true,
                        'description' => ucfirst($shiftType).' shift on '.$day.' for '.$hostel->name,
                    ]);
                }
            }
        }
    }

    private function getStartTime(string $shiftType): string
    {
        return match ($shiftType) {
            'morning' => '06:00:00',
            'afternoon' => '14:00:00',
            'night' => '22:00:00',
            'general' => '08:00:00',
            default => '08:00:00'
        };
    }

    private function getEndTime(string $shiftType): string
    {
        return match ($shiftType) {
            'morning' => '14:00:00',
            'afternoon' => '22:00:00',
            'night' => '06:00:00',
            'general' => '17:00:00',
            default => '17:00:00'
        };
    }
}
