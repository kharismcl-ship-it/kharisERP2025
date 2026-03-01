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
                'code'    => 'fleet_maintenance_completed',
                'name'    => 'Fleet: Maintenance Completed',
                'channel' => 'email',
                'subject' => 'Vehicle Maintenance Completed — {{vehicle_name}}',
                'body'    => "Dear Team,\n\nThe following maintenance has been completed:\n\nVehicle: {{vehicle_name}}\nType: {{maintenance_type}}\nService Date: {{service_date}}\nService Provider: {{service_provider}}\nCost: {{currency}} {{cost}}\nNext Service Due: {{next_service_date}}\n\nPlease update your records accordingly.\n\nRegards,\nFleet Management",
            ],
            [
                'code'        => 'fleet_maintenance_reminder',
                'name'        => 'Fleet: Upcoming Maintenance Reminder',
                'channel'     => 'email',
                'subject'     => 'Maintenance Due in {{days_until_service}} Days — {{vehicle_name}}',
                'body'        => "Dear Fleet Manager,\n\nThis is an automated reminder that the following vehicle is due for maintenance.\n\nVehicle:          {{vehicle_name}}\nPlate:            {{plate}}\nMaintenance Type: {{maintenance_type}}\nService Date:     {{next_service_date}}\nDays Remaining:   {{days_until_service}}\nCurrent Mileage:  {{current_mileage}} km\nReminder Date:    {{reminder_date}}\n\nPlease schedule the maintenance appointment at your earliest convenience.\n\nRegards,\nFleet Management System",
                'description' => 'Sent to fleet managers when a vehicle maintenance is approaching.',
            ],
        ];

        foreach ($templates as $tpl) {
            CommTemplate::updateOrCreate(
                ['code' => $tpl['code']],
                array_merge($tpl, ['is_active' => true])
            );
        }
    }
}