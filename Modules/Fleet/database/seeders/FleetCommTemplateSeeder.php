<?php

namespace Modules\Fleet\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class FleetCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug'     => 'fleet_maintenance_completed',
                'name'     => 'Fleet: Maintenance Completed',
                'channel'  => 'email',
                'subject'  => 'Vehicle Maintenance Completed — {{vehicle_name}}',
                'body'     => "Dear Team,\n\nThe following maintenance has been completed:\n\nVehicle: {{vehicle_name}}\nType: {{maintenance_type}}\nService Date: {{service_date}}\nService Provider: {{service_provider}}\nCost: {{currency}} {{cost}}\nNext Service Due: {{next_service_date}}\n\nPlease update your records accordingly.\n\nRegards,\nFleet Management",
                'is_active'=> true,
            ],
        ];

        foreach ($templates as $template) {
            CommTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}