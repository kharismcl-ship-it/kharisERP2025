<?php

namespace Modules\ITSupport\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class ITSupportCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'it_request_status_changed',
                'name'    => 'IT Support — Request Status Changed',
                'channel' => 'email',
                'subject' => 'IT Request {{reference}} — Status Updated to {{status}}',
                'body'    => "Dear {{requester}},\n\nYour IT support request has been updated.\n\nReference: {{reference}}\nSubject: {{subject}}\nNew Status: {{status}}\n\nPlease log in to the ERP system for more details or to provide additional information.",
            ],
            [
                'code'    => 'it_training_invite',
                'name'    => 'IT Support — Training Session Invitation',
                'channel' => 'email',
                'subject' => 'IT Training Invitation: {{title}}',
                'body'    => "Dear {{attendee}},\n\nYou are invited to the following IT training session:\n\nTitle: {{title}}\nDate & Time: {{scheduled_at}}\nLocation: {{location}}\n\nPlease confirm your attendance. For any questions, contact the IT department.",
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
