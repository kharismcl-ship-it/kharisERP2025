<?php

namespace Modules\Hostels\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelStaffShift;
use Modules\HR\Models\Employee;

class HostelStaffShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('hostel_shifts')) {
            return;
        }

        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $employees = Employee::whereHas('hostelAssignments')->get();
        if ($employees->isEmpty()) {
            $employees = Employee::limit(5)->get();
        }

        if ($employees->isEmpty()) {
            return;
        }

        $shiftTypes = ['morning', 'afternoon', 'night', 'general'];
        $statuses = ['scheduled', 'completed', 'absent'];

        // Generate shifts for the next 7 days
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays(6)->endOfDay();

        foreach ($hostels as $hostel) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                foreach ($shiftTypes as $shiftType) {
                    $employee = $employees->random();

                    HostelStaffShift::create([
                        'hostel_id' => $hostel->id,
                        'employee_id' => $employee->id,
                        'shift_type' => $shiftType,
                        'shift_date' => $currentDate->toDateString(),
                        'start_time' => $this->getStartTime($shiftType),
                        'end_time' => $this->getEndTime($shiftType),
                        'status' => $statuses[array_rand($statuses)],
                        'notes' => ucfirst($shiftType) . ' shift on ' . $currentDate->format('D, d M Y') . ' for ' . $hostel->name,
                    ]);
                }

                $currentDate->addDay();
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
