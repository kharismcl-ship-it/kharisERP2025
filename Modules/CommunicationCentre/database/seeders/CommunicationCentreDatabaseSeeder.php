<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;

class CommunicationCentreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DefaultCommProviderConfigSeeder::class,
            CommProviderConfigSeeder::class,
            CommTemplateSeeder::class,
            HostelCommTemplateSeeder::class,
            LeaveCommTemplateSeeder::class,
            FinanceCommTemplateSeeder::class,
            ProcurementCommTemplateSeeder::class,
            FleetCommTemplateSeeder::class,
            \Modules\Farms\Database\Seeders\FarmsCommTemplateSeeder::class,
            CommPreferenceSeeder::class,
        ]);
    }
}
