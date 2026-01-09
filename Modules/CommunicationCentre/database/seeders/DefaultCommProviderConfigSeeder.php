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
                'provider_name' => 'laravel_mail',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'channel' => 'sms',
                'provider_name' => 'mnotify',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'channel' => 'whatsapp',
                'provider_name' => 'wasender',
                'is_active' => true,
                'is_default' => true,
            ],
        ];

        foreach ($providers as $provider) {
            CommProviderConfig::updateOrCreate(
                [
                    'channel' => $provider['channel'],
                    'provider_name' => $provider['provider_name'],
                ],
                $provider
            );
        }
    }
}
