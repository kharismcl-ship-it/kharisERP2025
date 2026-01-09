<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelBlock;

class HostelBlockSeeder extends Seeder
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

        $blocks = [
            ['A Block', 'Main academic block with single rooms'],
            ['B Block', 'Shared accommodation block for students'],
            ['C Block', 'Premium block with ensuite facilities'],
            ['D Block', 'Graduate student accommodation'],
            ['E Block', 'International students block'],
        ];

        foreach ($hostels as $hostel) {
            foreach ($blocks as $index => $block) {
                HostelBlock::create([
                    'hostel_id' => $hostel->id,
                    'name' => $block[0],
                    'description' => $block[1],
                ]);
            }
        }
    }
}
