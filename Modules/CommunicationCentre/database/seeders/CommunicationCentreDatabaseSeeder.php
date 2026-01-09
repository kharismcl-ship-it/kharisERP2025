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
            CommPreferenceSeeder::class,
        ]);
    }
}
