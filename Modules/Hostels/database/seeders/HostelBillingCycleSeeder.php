<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelBillingCycle;

class HostelBillingCycleSeeder extends Seeder
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

        $cycleTypes = ['monthly', 'quarterly', 'semester', 'custom'];

        foreach ($hostels as $hostel) {
            for ($i = 1; $i <= 12; $i++) {
                $startDate = now()->subMonths($i)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();

                HostelBillingCycle::create([
                    'hostel_id' => $hostel->id,
                    'name' => 'Billing Cycle '.$startDate->format('F Y'),
                    'cycle_type' => $cycleTypes[array_rand($cycleTypes)],
                    'start_date'   => $startDate,
                    'end_date'     => $endDate,
                    'billing_date' => $startDate,
                    'due_date'     => $endDate->copy()->addDays(7),
                ]);
            }
        }
    }
}
