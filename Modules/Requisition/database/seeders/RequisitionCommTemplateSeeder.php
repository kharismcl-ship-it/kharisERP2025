<?php

namespace Modules\Requisition\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class RequisitionCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'requisition_status_changed',
                'name'    => 'Requisition — Status Changed',
                'channel' => 'email',
                'subject' => 'Requisition {{reference}} is now {{status}}',
                'body'    => "Dear {{requester}},\n\nYour request {{reference}} — {{title}} has been updated.\n\nNew Status: {{status}}\n\nPlease log in to the ERP system to view details.",
            ],
            [
                'code'    => 'requisition_shared_with_you',
                'name'    => 'Requisition — Shared With You',
                'channel' => 'email',
                'subject' => 'You have been added as {{role}} on requisition {{reference}}',
                'body'    => "Dear Team Member,\n\nYou have been added as a {{role}} on the following request:\n\nReference: {{reference}}\nTitle: {{title}}\nRaised by: {{requester}}\n\nPlease log in to review and take action.",
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
