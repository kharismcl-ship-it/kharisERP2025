<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelFloor;
use Modules\Hostels\Models\Room;

class RoomSeeder extends Seeder
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

        $floors = HostelFloor::all();
        if ($floors->isEmpty()) {
            $this->call(HostelFloorSeeder::class);
            $floors = HostelFloor::all();
        }

        $roomTypes = ['single', 'double', 'triple', 'quad'];
        $roomStatuses = ['available', 'occupied', 'maintenance', 'closed'];
        $genderPolicies = ['male', 'female', 'mixed'];

        foreach ($floors as $floor) {
            for ($i = 1; $i <= 20; $i++) {
                $roomNumber = $floor->level * 100 + $i;
                $roomType = $roomTypes[array_rand($roomTypes)];

                Room::create([
                    'hostel_id' => $floor->hostel_id,
                    'floor_id' => $floor->id,
                    'room_number' => (string) $roomNumber,
                    'type' => $roomType,
                    'base_rate' => $this->getBaseRate($roomType),
                    'status' => $roomStatuses[array_rand($roomStatuses)],
                    'gender_policy' => $genderPolicies[array_rand($genderPolicies)],
                    'billing_cycle' => 'per_semester',
                    'max_occupancy' => $this->getMaxOccupancy($roomType),
                    'current_occupancy' => rand(0, $this->getMaxOccupancy($roomType)),
                    'notes' => 'Room '.$roomNumber.' on floor '.$floor->level,
                ]);
            }
        }
    }

    private function getBaseRate(string $roomType): float
    {
        return match ($roomType) {
            'single' => 500.00,
            'double' => 800.00,
            'shared' => 300.00,
            'ensuite' => 700.00,
            'apartment' => 1200.00,
            default => 500.00,
        };
    }

    private function getMaxOccupancy(string $roomType): int
    {
        return match ($roomType) {
            'single' => 1,
            'double' => 2,
            'shared' => 4,
            'ensuite' => 2,
            'apartment' => 4,
            default => 2,
        };
    }
}
