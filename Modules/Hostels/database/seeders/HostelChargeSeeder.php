<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelFeeSetting;

class HostelChargeSeeder extends Seeder
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

        $chargeTypes = ['recurring', 'one_time'];

        foreach ($hostels as $hostel) {
            // Create a few different types of charges
            HostelFeeSetting::create([
                'hostel_id' => $hostel->id,
                'name' => 'Electricity Charge',
                'charge_type' => 'recurring',
                'amount' => rand(100, 500),
                'is_active' => true,
            ]);
            
            HostelFeeSetting::create([
                'hostel_id' => $hostel->id,
                'name' => 'Water Charge',
                'charge_type' => 'recurring',
                'amount' => rand(50, 300),
                'is_active' => true,
            ]);
            
            HostelFeeSetting::create([
                'hostel_id' => $hostel->id,
                'name' => 'Maintenance Fee',
                'charge_type' => 'one_time',
                'amount' => rand(200, 800),
                'is_active' => true,
            ]);
        }
    }
}
