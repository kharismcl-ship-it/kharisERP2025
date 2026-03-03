<?php

namespace Modules\Farms\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class FarmsCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Harvest Due — Email
            [
                'code'        => 'farms_harvest_due_email',
                'channel'     => 'email',
                'name'        => 'Farms: Harvest Due Alert (Email)',
                'subject'     => 'Harvest Due Alert — {{crop_name}} at {{farm_name}}',
                'body'        => <<<'EOD'
Dear Farm Manager,

This is a reminder that the crop cycle listed below is approaching or has passed its expected harvest date.

Farm:          {{farm_name}}
Crop:          {{crop_name}} ({{variety}})
Planted Area:  {{planted_area}}
Expected Date: {{expected_date}}
Status:        {{status}}

Please arrange for harvest assessment and action as needed.

Best regards,
Farm Management System
EOD,
                'description' => 'Sent to farm contact when a crop cycle is due or overdue for harvest.',
            ],

            // Harvest Due — SMS
            [
                'code'    => 'farms_harvest_due_sms',
                'channel' => 'sms',
                'name'    => 'Farms: Harvest Due Alert (SMS)',
                'subject' => null,
                'body'    => 'HARVEST ALERT: {{crop_name}} at {{farm_name}} is {{status}}. Expected: {{expected_date}}. Please take action.',
                'description' => 'SMS alert when crop harvest is due or overdue.',
            ],

            // Livestock Health Reminder — Email
            [
                'code'        => 'farms_livestock_health_reminder_email',
                'channel'     => 'email',
                'name'        => 'Farms: Livestock Health Reminder (Email)',
                'subject'     => 'Livestock Health Reminder — {{batch_reference}} at {{farm_name}}',
                'body'        => <<<'EOD'
Dear Farm Manager,

A livestock health treatment is due within the next 7 days. Please schedule accordingly.

Farm:          {{farm_name}}
Batch:         {{batch_reference}} ({{animal_type}})
Current Count: {{current_count}}
Treatment:     {{event_type}}
Medicine:      {{medicine_used}}
Due Date:      {{next_due_date}}

Please ensure the treatment is administered on time to maintain animal health.

Best regards,
Farm Management System
EOD,
                'description' => 'Sent when a livestock health treatment is due within 7 days.',
            ],

            // Livestock Health Reminder — SMS
            [
                'code'    => 'farms_livestock_health_reminder_sms',
                'channel' => 'sms',
                'name'    => 'Farms: Livestock Health Reminder (SMS)',
                'subject' => null,
                'body'    => 'HEALTH REMINDER: {{event_type}} due {{next_due_date}} for {{batch_reference}} ({{animal_type}}) at {{farm_name}}. Medicine: {{medicine_used}}.',
                'description' => 'SMS reminder for upcoming livestock health treatment.',
            ],

            // Task Overdue — Email
            [
                'code'        => 'farms_task_overdue_email',
                'channel'     => 'email',
                'name'        => 'Farms: Task Overdue Alert (Email)',
                'subject'     => 'Overdue Farm Task — {{task_title}} at {{farm_name}}',
                'body'        => <<<'EOD'
Dear Farm Manager,

The following farm task is overdue and requires immediate attention.

Farm:         {{farm_name}}
Task:         {{task_title}}
Type:         {{task_type}}
Priority:     {{priority}}
Due Date:     {{due_date}}
Assigned To:  {{assigned_to}}

Please complete or reschedule this task as soon as possible.

Best regards,
Farm Management System
EOD,
                'description' => 'Sent when a farm task becomes overdue.',
            ],

            // Task Overdue — SMS
            [
                'code'    => 'farms_task_overdue_sms',
                'channel' => 'sms',
                'name'    => 'Farms: Task Overdue Alert (SMS)',
                'subject' => null,
                'body'    => 'OVERDUE TASK: {{task_title}} at {{farm_name}} was due {{due_date}}. Priority: {{priority}}. Please action immediately.',
                'description' => 'SMS alert for overdue farm task.',
            ],
        ];

        // Phase 2 — Daily Report Submitted
        $templates[] = [
            'code'        => 'farms_daily_report_submitted',
            'channel'     => 'email',
            'name'        => 'Farms: Daily Report Submitted',
            'subject'     => 'Farm Daily Report Submitted — {{farm_name}} ({{report_date}})',
            'body'        => <<<'EOD'
Dear Farm Manager,

A daily report has been submitted for your farm.

Farm:             {{farm_name}}
Reporter:         {{worker_name}}
Report Date:      {{report_date}}
Weather:          {{weather}}

Summary:
{{summary}}

Activities Done:
{{activities_done}}

Issues Noted:
{{issues_noted}}

Please review and take any necessary action.

Best regards,
Farm Management System
EOD,
            'description' => 'Sent to company contact when a farm daily report is submitted.',
        ];

        // Phase 2 — Request Approved
        $templates[] = [
            'code'        => 'farms_request_approved',
            'channel'     => 'email',
            'name'        => 'Farms: Request Approved',
            'subject'     => 'Farm Request Approved — {{reference}} ({{title}})',
            'body'        => <<<'EOD'
Dear {{worker_name}},

Your farm request has been approved.

Farm:         {{farm_name}}
Reference:    {{reference}}
Title:        {{title}}
Type:         {{request_type}}
Urgency:      {{urgency}}
Status:       {{status}}

Notes:
{{notes}}

The procurement team will process this request shortly.

Best regards,
Farm Management System
EOD,
            'description' => 'Sent to requesting worker when a farm request is approved.',
        ];

        // Phase 2 — Request Rejected
        $templates[] = [
            'code'        => 'farms_request_rejected',
            'channel'     => 'email',
            'name'        => 'Farms: Request Rejected',
            'subject'     => 'Farm Request Rejected — {{reference}} ({{title}})',
            'body'        => <<<'EOD'
Dear {{worker_name}},

Unfortunately, your farm request has been rejected.

Farm:             {{farm_name}}
Reference:        {{reference}}
Title:            {{title}}
Type:             {{request_type}}
Status:           {{status}}

Reason for Rejection:
{{rejection_reason}}

Please contact your farm manager for further assistance.

Best regards,
Farm Management System
EOD,
            'description' => 'Sent to requesting worker when a farm request is rejected.',
        ];

        // Phase 2 — Worker Task Assigned
        $templates[] = [
            'code'        => 'farms_worker_task_assigned',
            'channel'     => 'email',
            'name'        => 'Farms: Task Assigned to Worker',
            'subject'     => 'Farm Task Assigned — {{task_title}} at {{farm_name}}',
            'body'        => <<<'EOD'
Dear {{worker_name}},

A new farm task has been assigned to you.

Farm:        {{farm_name}}
Task:        {{task_title}}
Type:        {{task_type}}
Priority:    {{priority}}
Due Date:    {{due_date}}

Description:
{{description}}

Please confirm receipt and complete the task by the due date.

Best regards,
Farm Management System
EOD,
            'description' => 'Sent to a farm worker when a task is assigned to them.',
        ];

        // Phase 2 — Crop Cycle Started
        $templates[] = [
            'code'        => 'farms_crop_cycle_started',
            'channel'     => 'email',
            'name'        => 'Farms: Crop Cycle Started',
            'subject'     => 'Crop Cycle Started — {{crop_name}} at {{farm_name}}',
            'body'        => <<<'EOD'
Dear Farm Manager,

A new crop cycle has been started on your farm.

Farm:            {{farm_name}}
Crop:            {{crop_name}} ({{variety}})
Planting Date:   {{planting_date}}
Expected Harvest:{{expected_harvest_date}}
Planted Area:    {{planted_area}}

Please ensure all inputs and activities are recorded in the system.

Best regards,
Farm Management System
EOD,
            'description' => 'Sent to company contact when a new crop cycle is started.',
        ];

        // Sale confirmation template
        $templates[] = [
            'code'        => 'farms_sale_confirmation',
            'channel'     => 'email',
            'name'        => 'Farms: Sale Confirmation',
            'subject'     => 'Farm Sale Confirmation — {{product_name}}',
            'body'        => <<<'EOD'
Dear {{buyer_name}},

Thank you for your purchase. Please find your sale confirmation below.

Farm:         {{farm_name}}
Product:      {{product_name}} ({{product_type}})
Quantity:     {{quantity}} {{unit}}
Unit Price:   {{currency}} {{unit_price}}
Total Amount: {{currency}} {{total_amount}}
Sale Date:    {{sale_date}}

An invoice will be issued for payment within 14 days.

Thank you for your business.

Best regards,
Farm Management
EOD,
            'description' => 'Sent to buyer when a farm sale record is created.',
        ];

        foreach ($templates as $data) {
            CommTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        $this->command->info('Farms comm templates seeded: ' . count($templates) . ' templates.');
    }
}