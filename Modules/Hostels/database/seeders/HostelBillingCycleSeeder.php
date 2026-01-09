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

        $cycleTypes = ['monthly', 'quarterly', 'semester', 'annual'];
        $statuses = ['active', 'closed', 'pending'];

        foreach ($hostels as $hostel) {
            for ($i = 1; $i <= 12; $i++) {
                $startDate = now()->subMonths($i)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();

                HostelBillingCycle::create([
                    'hostel_id' => $hostel->id,
                    'name' => 'Billing Cycle '.$startDate->format('F Y'),
                    'cycle_type' => $cycleTypes[array_rand($cycleTypes)],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'due_date' => $endDate->copy()->addDays(7),
                    'status' => $statuses[array_rand($statuses)],
                    'total_amount' => rand(50000, 200000) / 100,
                    'total_collected' => rand(40000, 180000) / 100,
                    'total_outstanding' => rand(0, 20000) / 100,
                    'notes' => 'Billing cycle for '.$startDate->format('F Y').' at '.$hostel->name,
                ]);
            }
        }
    }
}
