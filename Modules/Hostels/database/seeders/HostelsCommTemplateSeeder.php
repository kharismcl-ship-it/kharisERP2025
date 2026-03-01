<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class HostelsCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Deposit Reminder (SMS — used by DepositReminderHandler)
            [
                'code'        => 'hostel_deposit_reminder',
                'channel'     => 'sms',
                'name'        => 'Hostels: Deposit Collection Reminder (SMS)',
                'subject'     => null,
                'body'        => 'Reminder: GHS {{deposit_amount}} deposit for {{hostel_name}} is outstanding (Ref: {{booking_reference}}). Please pay promptly to secure your accommodation.',
                'description' => 'SMS reminder for pending hostel deposit.',
            ],

            // Overdue Charge Reminder (SMS — used by OverdueChargeReminderHandler)
            [
                'code'        => 'hostel_overdue_charge_reminder',
                'channel'     => 'sms',
                'name'        => 'Hostels: Overdue Charge Reminder (SMS)',
                'subject'     => null,
                'body'        => 'REMINDER: GHS {{outstanding_amount}} outstanding on your hostel account (Ref: {{booking_reference}}). Please pay promptly.',
                'description' => 'SMS reminder for outstanding hostel balance.',
            ],
        ];

        foreach ($templates as $data) {
            CommTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        $this->command->info('Hostels comm templates seeded: ' . count($templates) . ' templates.');
    }
}