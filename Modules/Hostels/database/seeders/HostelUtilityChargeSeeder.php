<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelUtilityCharge;

class HostelUtilityChargeSeeder extends Seeder
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

        $utilityTypes = ['electricity', 'water', 'internet', 'gas', 'sewage'];
        $billingPeriods = ['monthly', 'bimonthly', 'quarterly'];
        $statuses = ['pending', 'billed', 'paid', 'overdue'];

        foreach ($hostels as $hostel) {
            foreach ($utilityTypes as $utilityType) {
                HostelUtilityCharge::create([
                    'hostel_id' => $hostel->id,
                    'utility_type' => $utilityType,
                    'billing_period' => $billingPeriods[array_rand($billingPeriods)],
                    'start_date' => now()->subMonths(rand(1, 12))->startOfMonth(),
                    'end_date' => now()->subMonths(rand(0, 11))->endOfMonth(),
                    'consumption' => rand(100, 1000),
                    'rate' => rand(1, 10) / 10,
                    'amount' => rand(500, 5000),
                    'status' => $statuses[array_rand($statuses)],
                    'due_date' => now()->addDays(rand(1, 30)),
                    'paid_date' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                    'notes' => $utilityType.' charges for '.$hostel->name,
                ]);
            }
        }
    }
}
