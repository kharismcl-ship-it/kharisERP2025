<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class LeaveCommTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Leave Request Submitted
            [
                'code' => 'leave_request_submitted',
                'channel' => 'email',
                'name' => 'Leave Request Submitted',
                'subject' => 'Leave Request Submitted - {{employee_name}}',
                'body' => <<<'EOD'
Dear {{manager_name}},

{{employee_name}} has submitted a leave request for {{leave_type}}.

Details:
- Start Date: {{start_date}}
- End Date: {{end_date}}
- Duration: {{duration}} days
- Reason: {{reason}}

Please review and approve/reject this request in the HR system.

Best regards,
HR System
EOD,
                'description' => 'Notification sent to manager when an employee submits a leave request',
            ],

            // Leave Request Approved
            [
                'code' => 'leave_request_approved',
                'channel' => 'email',
                'name' => 'Leave Request Approved',
                'subject' => 'Leave Request Approved - {{employee_name}}',
                'body' => <<<'EOD'
Dear {{employee_name}},

Your leave request has been approved by {{manager_name}}.

Details:
- Leave Type: {{leave_type}}
- Start Date: {{start_date}}
- End Date: {{end_date}}
- Duration: {{duration}} days
- Remaining Balance: {{remaining_balance}} days

Enjoy your time off!

Best regards,
HR Department
EOD,
                'description' => 'Notification sent to employee when leave request is approved',
            ],

            // Leave Request Rejected
            [
                'code' => 'leave_request_rejected',
                'channel' => 'email',
                'name' => 'Leave Request Rejected',
                'subject' => 'Leave Request Not Approved - {{employee_name}}',
                'body' => <<<'EOD'
Dear {{employee_name}},

Your leave request has been reviewed and unfortunately cannot be approved at this time.

Details:
- Leave Type: {{leave_type}}
- Start Date: {{start_date}}
- End Date: {{end_date}}
- Duration: {{duration}} days
- Reason for rejection: {{rejection_reason}}

Please contact your manager {{manager_name}} if you have any questions.

Best regards,
HR Department
EOD,
                'description' => 'Notification sent to employee when leave request is rejected',
            ],

            // Leave Balance Low
            [
                'code' => 'leave_balance_low',
                'channel' => 'email',
                'name' => 'Leave Balance Low',
                'subject' => 'Low Leave Balance Alert - {{employee_name}}',
                'body' => <<<'EOD'
Dear {{employee_name}},

Your {{leave_type}} leave balance is running low.

Current Balance: {{current_balance}} days
Minimum Threshold: {{threshold}} days

Please plan your leave requests accordingly.

Best regards,
HR System
EOD,
                'description' => 'Notification sent to employee when leave balance falls below threshold',
            ],

            // Leave Request Cancelled
            [
                'code' => 'leave_request_cancelled',
                'channel' => 'email',
                'name' => 'Leave Request Cancelled',
                'subject' => 'Leave Request Cancelled - {{employee_name}}',
                'body' => <<<'EOD'
Dear {{manager_name}},

{{employee_name}} has cancelled their leave request.

Original Details:
- Leave Type: {{leave_type}}
- Start Date: {{start_date}}
- End Date: {{end_date}}
- Duration: {{duration}} days

Best regards,
HR System
EOD,
                'description' => 'Notification sent to manager when employee cancels leave request',
            ],

            // SMS Templates
            [
                'code' => 'leave_approval_sms',
                'channel' => 'sms',
                'name' => 'Leave Approval SMS',
                'subject' => null,
                'body' => 'Hi {{employee_name}}, your {{leave_type}} leave from {{start_date}} to {{end_date}} has been approved. Balance: {{remaining_balance}} days.',
                'description' => 'SMS notification for leave approval',
            ],

            [
                'code' => 'leave_rejection_sms',
                'channel' => 'sms',
                'name' => 'Leave Rejection SMS',
                'subject' => null,
                'body' => 'Hi {{employee_name}}, your leave request was not approved. Contact {{manager_name}} for details.',
                'description' => 'SMS notification for leave rejection',
            ],

            [
                'code' => 'leave_balance_low_sms',
                'channel' => 'sms',
                'name' => 'Leave Balance Low SMS',
                'subject' => null,
                'body' => 'Hi {{employee_name}}, your {{leave_type}} leave balance is low: {{current_balance}} days remaining. Threshold: {{threshold}} days.',
                'description' => 'SMS notification for low leave balance',
            ],

            // WhatsApp Templates
            [
                'code' => 'leave_approval_whatsapp',
                'channel' => 'whatsapp',
                'name' => 'Leave Approval WhatsApp',
                'subject' => null,
                'body' => '✅ Leave Approved\n\nHi {{employee_name}}, your {{leave_type}} leave has been approved!\n\n📅 Dates: {{start_date}} to {{end_date}}\n⏰ Duration: {{duration}} days\n📊 Remaining Balance: {{remaining_balance}} days\n\nEnjoy your time off! 🎉',
                'description' => 'WhatsApp notification for leave approval',
            ],

            [
                'code' => 'leave_rejection_whatsapp',
                'channel' => 'whatsapp',
                'name' => 'Leave Rejection WhatsApp',
                'subject' => null,
                'body' => '❌ Leave Not Approved\n\nHi {{employee_name}}, your leave request was not approved.\n\n📅 Dates: {{start_date}} to {{end_date}}\n⏰ Duration: {{duration}} days\n📋 Reason: {{rejection_reason}}\n\nPlease contact {{manager_name}} for details.',
                'description' => 'WhatsApp notification for leave rejection',
            ],

            [
                'code' => 'leave_balance_low_whatsapp',
                'channel' => 'whatsapp',
                'name' => 'Leave Balance Low WhatsApp',
                'subject' => null,
                'body' => '⚠️ Low Leave Balance\n\nHi {{employee_name}}, your {{leave_type}} leave balance is running low.\n\n📊 Current Balance: {{current_balance}} days\n📉 Minimum Threshold: {{threshold}} days\n\nPlease plan your leave requests accordingly.',
                'description' => 'WhatsApp notification for low leave balance',
            ],
        ];

        foreach ($templates as $templateData) {
            CommTemplate::firstOrCreate(
                ['code' => $templateData['code']],
                $templateData
            );
        }
    }
}
