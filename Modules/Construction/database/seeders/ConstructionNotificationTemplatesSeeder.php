<?php

namespace Modules\Construction\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class ConstructionNotificationTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'construction_worker_checked_in',
                'name'    => 'Construction — Worker Checked In',
                'channel' => 'sms',
                'subject' => null,
                'body'    => '{{worker_name}} checked in at {{check_in_time}} on {{project_name}} ({{date}}).',
            ],
            [
                'code'    => 'construction_worker_checked_out',
                'name'    => 'Construction — Worker Checked Out',
                'channel' => 'sms',
                'subject' => null,
                'body'    => '{{worker_name}} checked out. Hours: {{hours_worked}}. Per diem: GHS {{per_diem_amount}}.',
            ],
            [
                'code'    => 'construction_contractor_request_submitted',
                'name'    => 'Construction — Contractor Request Submitted',
                'channel' => 'email',
                'subject' => 'New {{request_type}} Request: {{request_title}}',
                'body'    => "A new contractor request has been submitted.\n\nProject: {{project_name}}\nContractor: {{contractor_name}}\nRequest Type: {{request_type}}\nTitle: {{request_title}}\nPriority: {{priority}}\n\nDescription:\n{{description}}\n\nPlease review and approve or reject this request.",
            ],
            [
                'code'    => 'construction_contractor_request_approved',
                'name'    => 'Construction — Contractor Request Approved',
                'channel' => 'email',
                'subject' => 'Request Approved: {{request_title}}',
                'body'    => "Dear {{contractor_name}},\n\nYour request has been approved.\n\nProject: {{project_name}}\nRequest: {{request_title}}\nType: {{request_type}}\nApproved Amount: {{approved_amount}}\n\nNotes: {{approval_notes}}\n\nPlease proceed accordingly.",
            ],
            [
                'code'    => 'construction_contractor_request_approved_sms',
                'name'    => 'Construction — Contractor Request Approved (SMS)',
                'channel' => 'sms',
                'subject' => null,
                'body'    => 'Your request "{{request_title}}" on {{project_name}} has been APPROVED. Amount: {{approved_amount}}.',
            ],
            [
                'code'    => 'construction_contractor_request_rejected',
                'name'    => 'Construction — Contractor Request Rejected',
                'channel' => 'email',
                'subject' => 'Request Update: {{request_title}}',
                'body'    => "Dear {{contractor_name}},\n\nUnfortunately, your request could not be approved at this time.\n\nProject: {{project_name}}\nRequest: {{request_title}}\nType: {{request_type}}\n\nReason: {{approval_notes}}\n\nPlease contact your project manager for further information.",
            ],
            [
                'code'    => 'construction_monitoring_report_submitted',
                'name'    => 'Construction — Monitoring Report Submitted',
                'channel' => 'email',
                'subject' => 'New Site Report: {{project_name}} — {{visit_date}}',
                'body'    => "A site monitoring report has been submitted.\n\nProject: {{project_name}}\nMonitor: {{monitor_name}}\nVisit Date: {{visit_date}}\nCompliance Score: {{compliance_score}}\n\nKey Findings:\n{{findings_summary}}\n\nPlease log in to review the full report.",
            ],
        ];

        foreach ($templates as $tpl) {
            CommTemplate::updateOrCreate(
                ['code' => $tpl['code']],
                array_merge($tpl, ['is_active' => true, 'company_id' => null])
            );
        }
    }
}
