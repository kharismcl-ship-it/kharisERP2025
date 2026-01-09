<?php

namespace Modules\PaymentsChannel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PaymentsChannel\Models\PayMethod;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class FlutterwaveConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Flutterwave provider configuration
        $providerConfig = PayProviderConfig::updateOrCreate(
            [
                'provider' => 'flutterwave',
                'company_id' => 1, // Assuming company ID 1, adjust as needed
            ],
            [
                'name' => 'Flutterwave',
                'is_default' => true,
                'is_active' => true,
                'mode' => 'sandbox', // Use 'live' for production
                'config' => [
                    'public_key' => 'FLWPUBK_TEST-1d8ee3cb19c901246471f75341552fc7-X',
                    'secret_key' => 'FLWSECK_TEST-69dc70d0ac490e0f12505d54f6342a27-X',
                ],
            ]
        );

        // Create a payment method using Flutterwave
        $payMethod = PayMethod::updateOrCreate(
            [
                'code' => 'flutterwave_online',
                'company_id' => 1, // Assuming company ID 1, adjust as needed
            ],
            [
                'name' => 'Flutterwave Online Payment',
                'provider' => 'flutterwave',
                'channel' => 'online',
                'payment_mode' => 'online',
                'currency' => 'GHS', // Ghana Cedis, change as needed
                'is_active' => true,
                'sort_order' => 0,
                'config' => [],
            ]
        );

        $this->command->info('Flutterwave configuration seeded successfully.');
    }
}
