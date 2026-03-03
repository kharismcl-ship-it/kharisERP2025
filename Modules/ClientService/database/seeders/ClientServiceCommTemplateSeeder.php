<?php

namespace Modules\ClientService\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class ClientServiceCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'visitor_checkout_message',
                'name'    => 'Client Service — Visitor Checkout',
                'channel' => 'sms',
                'subject' => 'Thank you for visiting {{company_name}}',
                'body'    => "Thank you for visiting {{company_name}}, {{visitor_name}}. Safe travels! We hope to see you again.",
            ],
            [
                'code'    => 'visitor_contact_info',
                'name'    => 'Client Service — Visitor Contact Info',
                'channel' => 'email',
                'subject' => '{{company_name}} Contact Information',
                'body'    => "Dear {{visitor_name}},\n\nYou requested {{company_name}}'s contact details:\n\n{{contact_details}}\n\nThank you for visiting us.",
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
