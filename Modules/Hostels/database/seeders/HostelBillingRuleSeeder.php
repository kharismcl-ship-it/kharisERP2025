<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelBillingRule;

class HostelBillingRuleSeeder extends Seeder
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

        $ruleTypes = ['room_rate', 'utility_charge', 'service_fee', 'penalty', 'discount'];
        $calculationMethods = ['fixed', 'percentage', 'per_day', 'per_person'];
        $statuses = ['active', 'inactive'];

        foreach ($hostels as $hostel) {
            foreach ($ruleTypes as $ruleType) {
                HostelBillingRule::create([
                    'hostel_id' => $hostel->id,
                    'name' => ucfirst(str_replace('_', ' ', $ruleType)).' Rule',
                    'type' => $ruleType,
                    'calculation_method' => $calculationMethods[array_rand($calculationMethods)],
                    'amount' => rand(10, 500),
                    'is_active' => true,
                    'description' => 'Billing rule for '.$ruleType.' at '.$hostel->name,
                    'applicable_from' => now()->subMonths(rand(1, 6)),
                    'applicable_to' => now()->addMonths(rand(6, 12)),
                    'priority' => rand(1, 10),
                ]);
            }
        }
    }
}
