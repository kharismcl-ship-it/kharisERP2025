<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommProviderConfig;

class DefaultCommProviderConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = [
            [
                'channel' => 'email',
                'provider' => 'laravel_mail',
                'name' => 'Laravel Mail',
                'is_active' => true,
                'is_default' => true,
                'config' => [],
            ],
            [
                'channel' => 'sms',
                'provider' => 'mnotify',
                'name' => 'Mnotify SMS',
                'is_active' => true,
                'is_default' => true,
                'config' => [],
            ],
            [
                'channel' => 'whatsapp',
                'provider' => 'wasender',
                'name' => 'Wasender WhatsApp',
                'is_active' => true,
                'is_default' => true,
                'config' => [
                    'base_url' => 'https://api.wasender.example.com',
                    'api_key' => 'test-api-key',
                    'device_id' => 'test-device-id',
                ],
            ],
        ];

        foreach ($providers as $provider) {
            CommProviderConfig::updateOrCreate(
                [
                    'channel' => $provider['channel'],
                    'provider' => $provider['provider'],
                ],
                $provider
            );
        }
    }
}
