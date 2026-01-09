<?php

namespace Modules\PaymentsChannel\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PaymentsChannel\Models\PayMethod;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class ManualPaymentConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Manual provider configuration
        $providerConfig = PayProviderConfig::updateOrCreate(
            [
                'provider' => 'manual',
                'company_id' => null, // Global configuration
            ],
            [
                'name' => 'Manual Payments',
                'is_default' => false,
                'is_active' => true,
                'mode' => 'live', // Mode is not really used for manual payments
                'config' => [
                    'verification_required' => true,
                    'notification_emails' => ['finance@company.com', 'admin@company.com'],
                    'processing_time' => '1-2 business days',
                ],
            ]
        );

        // Create a manual payment method for Bank Transfer
        $bankTransferMethod = PayMethod::updateOrCreate(
            [
                'code' => 'manual_bank',
                'company_id' => 1, // Using company ID 1 as default
            ],
            [
                'name' => 'Manual Bank Transfer',
                'provider' => 'manual',
                'channel' => 'bank',
                'payment_mode' => 'offline',
                'currency' => 'GHS', // Ghana Cedis, change as needed
                'is_active' => true,
                'sort_order' => 10,
                'offline_payment_instruction' => 'Please transfer the amount to the following bank account:<br>Bank: Example Bank<br>Account Name: Company Name<br>Account Number: 1234567890<br>Branch Code: 001<br>Swift Code: EXAMGHAC<br><br>Include your payment reference number in the transfer description. Processing time: 1-2 business days.',
                'config' => [
                    'instructions' => 'Please transfer the amount to the following bank account:',
                    'account_details' => [
                        'bank_name' => 'Example Bank',
                        'account_name' => 'Company Name',
                        'account_number' => '1234567890',
                        'branch_code' => '001',
                        'swift_code' => 'EXAMGHAC',
                    ],
                    'processing_time' => '1-2 business days',
                    'additional_notes' => 'Include your payment reference number in the transfer description.',
                ],
            ]
        );

        // Create a manual payment method for Cash
        $cashMethod = PayMethod::updateOrCreate(
            [
                'code' => 'manual_cash',
                'company_id' => 1, // Using company ID 1 as default
            ],
            [
                'name' => 'Manual Cash Payment',
                'provider' => 'manual',
                'channel' => 'cash',
                'payment_mode' => 'offline',
                'currency' => 'GHS', // Ghana Cedis, change as needed
                'is_active' => true,
                'sort_order' => 11,
                'offline_payment_instruction' => 'Please make cash payment at our office during business hours.\nOffice Address: 123 Business Street, Accra, Ghana\nBusiness Hours: Monday-Friday: 9:00 AM - 5:00 PM\nContact Person: Finance Department\nContact Phone: +233 30 123 4567',
                'config' => [
                    'instructions' => 'Please make cash payment at our office during business hours.',
                    'office_address' => '123 Business Street, Accra, Ghana',
                    'business_hours' => 'Monday-Friday: 9:00 AM - 5:00 PM',
                    'contact_person' => 'Finance Department',
                    'contact_phone' => '+233 30 123 4567',
                ],
            ]
        );

        $this->command->info('Manual payment configuration seeded successfully.');
    }
}
