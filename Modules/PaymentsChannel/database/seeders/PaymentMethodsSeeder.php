<?php

namespace Modules\PaymentsChannel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PaymentsChannel\Models\PayMethod;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create payment methods
        $paymentMethods = [
            [
                'code' => 'flutterwave_momo',
                'name' => 'Mobile Money (Flutterwave)',
                'provider' => 'flutterwave',
                'channel' => 'momo',
                'payment_mode' => 'online',
                'currency' => 'GHS',
                'is_active' => true,
                'sort_order' => 0,
                'company_id' => 1,
                'config' => [],
            ],
            [
                'code' => 'flutterwave_card',
                'name' => 'Credit/Debit Card (Flutterwave)',
                'provider' => 'flutterwave',
                'channel' => 'card',
                'payment_mode' => 'online',
                'currency' => 'GHS',
                'is_active' => true,
                'sort_order' => 1,
                'company_id' => 1,
                'config' => [],
            ],
            [
                'code' => 'flutterwave_bank',
                'name' => 'Bank Transfer (Flutterwave)',
                'provider' => 'flutterwave',
                'channel' => 'bank',
                'payment_mode' => 'online',
                'currency' => 'GHS',
                'is_active' => true,
                'sort_order' => 2,
                'company_id' => 1,
                'config' => [],
            ],
        ];

        foreach ($paymentMethods as $methodData) {
            PayMethod::updateOrCreate(
                [
                    'code' => $methodData['code'],
                    'company_id' => $methodData['company_id'],
                ],
                $methodData
            );
        }

        $this->command->info('Payment methods seeded successfully.');
    }
}
