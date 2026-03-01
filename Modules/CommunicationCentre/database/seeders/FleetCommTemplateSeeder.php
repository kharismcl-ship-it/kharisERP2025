<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class FleetCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── Document Expiry ──────────────────────────────────────────────
            [
                'code'        => 'fleet_document_expiry',
                'name'        => 'Fleet — Document Expiry Alert (Email)',
                'channel'     => 'email',
                'subject'     => 'Action Required: {{document_type}} for {{vehicle_name}} ({{plate_number}}) expires in {{days_remaining}} day(s)',
                'body'        => "Dear {{recipient_name}},\n\nThis is an automated reminder that the following vehicle document is expiring soon:\n\n"
                               . "Vehicle:         {{vehicle_name}} ({{plate_number}})\n"
                               . "Document Type:   {{document_type}}\n"
                               . "Document Number: {{document_number}}\n"
                               . "Expiry Date:     {{expiry_date}}\n"
                               . "Days Remaining:  {{days_remaining}}\n\n"
                               . "Please arrange renewal before the expiry date to ensure the vehicle remains compliant and operational.\n\n"
                               . "This is an automated alert from the Fleet Management system.",
                'variables'   => ['vehicle_name', 'plate_number', 'document_type', 'document_number', 'expiry_date', 'days_remaining', 'recipient_name'],
                'is_active'   => true,
            ],
            [
                'code'        => 'fleet_document_expiry_sms',
                'name'        => 'Fleet — Document Expiry Alert (SMS)',
                'channel'     => 'sms',
                'subject'     => null,
                'body'        => 'FLEET ALERT: {{document_type}} for {{vehicle_name}} ({{plate_number}}) expires {{expiry_date}} ({{days_remaining}} day(s)). Please renew immediately.',
                'variables'   => ['vehicle_name', 'plate_number', 'document_type', 'expiry_date', 'days_remaining'],
                'is_active'   => true,
            ],

            // ── Service Due ──────────────────────────────────────────────────
            [
                'code'        => 'fleet_service_due',
                'name'        => 'Fleet — Service Due Alert (Email)',
                'channel'     => 'email',
                'subject'     => 'Service Reminder: {{service_type}} for {{vehicle_name}} ({{plate_number}}) scheduled in {{days_until}} day(s)',
                'body'        => "Dear {{recipient_name}},\n\nA scheduled maintenance service is due soon:\n\n"
                               . "Vehicle:          {{vehicle_name}} ({{plate_number}})\n"
                               . "Service Type:     {{service_type}}\n"
                               . "Scheduled Date:   {{service_date}}\n"
                               . "Days Until:       {{days_until}}\n"
                               . "Service Provider: {{service_provider}}\n"
                               . "Description:      {{description}}\n\n"
                               . "Please ensure the vehicle is available and confirm the booking with the service provider.\n\n"
                               . "This is an automated alert from the Fleet Management system.",
                'variables'   => ['vehicle_name', 'plate_number', 'service_type', 'service_date', 'service_provider', 'description', 'days_until', 'recipient_name'],
                'is_active'   => true,
            ],
            [
                'code'        => 'fleet_service_due_sms',
                'name'        => 'Fleet — Service Due Alert (SMS)',
                'channel'     => 'sms',
                'subject'     => null,
                'body'        => 'FLEET: {{service_type}} for {{vehicle_name}} ({{plate_number}}) due {{service_date}} ({{days_until}} day(s)). Confirm with {{service_provider}}.',
                'variables'   => ['vehicle_name', 'plate_number', 'service_type', 'service_date', 'service_provider', 'days_until'],
                'is_active'   => true,
            ],

            // ── Maintenance Completed ────────────────────────────────────────
            [
                'code'        => 'fleet_maintenance_completed',
                'name'        => 'Fleet — Maintenance Completed (Email)',
                'channel'     => 'email',
                'subject'     => 'Service Complete: {{vehicle_name}} ({{plate_number}}) is back in active service',
                'body'        => "Dear {{recipient_name}},\n\nThe following maintenance job has been completed and the vehicle is now active:\n\n"
                               . "Vehicle:         {{vehicle_name}} ({{plate_number}})\n"
                               . "Service Type:    {{service_type}}\n"
                               . "Completed On:    {{service_date}}\n"
                               . "Service Cost:    GHS {{cost}}\n"
                               . "Service Provider:{{service_provider}}\n\n"
                               . "The vehicle has been restored to Active status and is ready for deployment.\n\n"
                               . "This is an automated notification from the Fleet Management system.",
                'variables'   => ['vehicle_name', 'plate_number', 'service_type', 'service_date', 'cost', 'service_provider', 'recipient_name'],
                'is_active'   => true,
            ],

            // ── Vehicle Assigned ─────────────────────────────────────────────
            [
                'code'        => 'fleet_vehicle_assigned',
                'name'        => 'Fleet — Vehicle Assigned to Driver (Email)',
                'channel'     => 'email',
                'subject'     => 'Vehicle Assignment: {{vehicle_name}} ({{plate_number}}) assigned to you from {{assigned_from}}',
                'body'        => "Dear {{driver_name}},\n\nYou have been assigned a vehicle. Please review the details below:\n\n"
                               . "Vehicle:      {{vehicle_name}} ({{plate_number}})\n"
                               . "Type:         {{vehicle_type}}\n"
                               . "Fuel Type:    {{fuel_type}}\n"
                               . "Assigned From:{{assigned_from}}\n"
                               . "Assigned Until:{{assigned_until}}\n\n"
                               . "Please acknowledge this assignment and ensure the vehicle is properly maintained during your assignment period.\n\n"
                               . "This is an automated notification from the Fleet Management system.",
                'variables'   => ['driver_name', 'vehicle_name', 'plate_number', 'vehicle_type', 'fuel_type', 'assigned_from', 'assigned_until'],
                'is_active'   => true,
            ],
        ];

        foreach ($templates as $data) {
            CommTemplate::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
