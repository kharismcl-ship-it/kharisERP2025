<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\FeeType;
use Modules\Hostels\Models\Hostel;

class FeeTypeSeeder extends Seeder
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

        $feeTypes = [
            ['Security Deposit', 'security_deposit', 'one_time', 500.00],
            ['Utility Fee', 'utility_fee', 'per_semester', 100.00],
            ['Maintenance Fee', 'maintenance_fee', 'per_semester', 50.00],
            ['Cleaning Fee', 'cleaning_fee', 'per_semester', 30.00],
            ['Internet Fee', 'internet_fee', 'per_semester', 80.00],
            ['Late Payment Fee', 'late_fee', 'one_time', 25.00],
            ['Damage Fee', 'damage_fee', 'one_time', 0.00],
            ['Booking Fee', 'booking_fee', 'one_time', 20.00],
        ];

        foreach ($hostels as $hostel) {
            foreach ($feeTypes as $feeType) {
                FeeType::create([
                    'hostel_id' => $hostel->id,
                    'name' => $feeType[0],
                    'code' => $feeType[1],
                    'billing_cycle' => $feeType[2],
                    'default_amount' => $feeType[3],
                    'is_mandatory' => true,
                    'is_active' => true,
                ]);
            }
        }
    }
}
