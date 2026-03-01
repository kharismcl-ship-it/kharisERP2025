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

        $utilityTypes = ['electricity', 'water', 'internet', 'gas', 'maintenance'];
        $statuses = ['pending', 'billed', 'paid', 'overdue'];

        foreach ($hostels as $hostel) {
            foreach ($utilityTypes as $utilityType) {
                $periodStart = now()->subMonths(rand(1, 12))->startOfMonth();
                $periodEnd = $periodStart->copy()->endOfMonth();
                $consumption = rand(100, 1000);
                $ratePerUnit = rand(1, 10) / 10;

                HostelUtilityCharge::create([
                    'hostel_id' => $hostel->id,
                    'utility_type' => $utilityType,
                    'billing_period_start' => $periodStart,
                    'billing_period_end' => $periodEnd,
                    'consumption' => $consumption,
                    'rate_per_unit' => $ratePerUnit,
                    'total_amount' => round($consumption * $ratePerUnit, 2),
                    'status' => $statuses[array_rand($statuses)],
                    'due_date' => $periodEnd->copy()->addDays(rand(7, 30)),
                ]);
            }
        }
    }
}
