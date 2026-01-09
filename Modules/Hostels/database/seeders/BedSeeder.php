<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Room;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->call(RoomSeeder::class);
            $rooms = Room::all();
        }

        $bedStatuses = ['available', 'occupied', 'maintenance', 'reserved'];

        foreach ($rooms as $room) {
            $bedCount = $room->max_occupancy;

            for ($i = 1; $i <= $bedCount; $i++) {
                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => $i,
                    'status' => $bedStatuses[array_rand($bedStatuses)],
                    'notes' => 'Bed '.$i.' in room '.$room->room_number,
                ]);
            }
        }
    }
}
