<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelBlock;
use Modules\Hostels\Models\HostelFloor;

class HostelFloorSeeder extends Seeder
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

        // Ensure hostel blocks exist
        $blocks = HostelBlock::all();
        if ($blocks->isEmpty()) {
            $this->call(HostelBlockSeeder::class);
            $blocks = HostelBlock::all();
        }

        foreach ($blocks as $block) {
            for ($i = 1; $i <= 4; $i++) {
                HostelFloor::create([
                    'hostel_id' => $block->hostel_id,
                    'hostel_block_id' => $block->id,
                    'name' => 'Floor '.$i,
                    'level' => $i,
                ]);
            }
        }
    }
}
