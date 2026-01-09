<?php

namespace Modules\PaymentsChannel\Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentsChannelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            FlutterwaveConfigSeeder::class,
            PaymentMethodsSeeder::class,
            ManualPaymentConfigSeeder::class,
        ]);
    }
}
