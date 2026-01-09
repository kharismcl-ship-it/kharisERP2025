<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommProviderConfig;

class CommProviderConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default provider configurations
        $providers = [
            [
                'channel' => 'email',
                'provider' => 'laravel_mail',
                'name' => 'Default Email Provider',
                'is_default' => true,
                'is_active' => true,
                'config' => [
                    'driver' => 'smtp',
                    'host' => 'smtp.example.com',
                    'port' => 587,
                    'encryption' => 'tls',
                    'username' => 'your_username',
                    'password' => 'your_password',
                ],
            ],
            [
                'channel' => 'sms',
                'provider' => 'mnotify',
                'name' => 'Default SMS Provider',
                'is_default' => true,
                'is_active' => true,
                'config' => [
                    'api_key' => 'your_mnotify_api_key',
                    'sender_id' => 'KharisERP',
                ],
            ],
            [
                'channel' => 'whatsapp',
                'provider' => 'twilio',
                'name' => 'Default WhatsApp Provider',
                'is_default' => true,
                'is_active' => true,
                'config' => [
                    'account_sid' => 'your_twilio_account_sid',
                    'auth_token' => 'your_twilio_auth_token',
                    'from_number' => '+1234567890',
                ],
            ],
        ];

        foreach ($providers as $providerData) {
            CommProviderConfig::firstOrCreate(
                [
                    'channel' => $providerData['channel'],
                    'provider' => $providerData['provider'],
                    'is_default' => $providerData['is_default'],
                ],
                $providerData
            );
        }
    }
}
