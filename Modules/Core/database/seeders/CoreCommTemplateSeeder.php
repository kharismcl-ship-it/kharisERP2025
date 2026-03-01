<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class CoreCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Payment Completed (email)
            [
                'code'        => 'core_payment_completed',
                'channel'     => 'email',
                'name'        => 'Core: Payment Completed (Email)',
                'subject'     => 'Payment Confirmed — {{invoice_number}}',
                'body'        => <<<'EOD'
Dear {{customer_name}},

Your payment has been successfully processed. Please retain this confirmation for your records.

Invoice:       {{invoice_number}}
Amount Paid:   {{currency}} {{amount}}
Reference:     {{reference}}
Date:          {{payment_date}}
Module:        {{module}}

Thank you for your payment.

Best regards,
Finance Team
EOD,
                'description' => 'Sent to customer when a payment is successfully completed through the payment orchestration service.',
            ],

            // Payment Completed (SMS)
            [
                'code'    => 'core_payment_completed_sms',
                'channel' => 'sms',
                'name'    => 'Core: Payment Completed (SMS)',
                'subject' => null,
                'body'    => 'Payment confirmed: {{currency}} {{amount}} for Invoice {{invoice_number}} on {{payment_date}}. Ref: {{reference}}. Thank you.',
                'description' => 'SMS confirmation when a payment is successfully completed.',
            ],

            // Payment Failed (email)
            [
                'code'        => 'core_payment_failed',
                'channel'     => 'email',
                'name'        => 'Core: Payment Failed (Email)',
                'subject'     => 'Payment Failed — {{invoice_number}}',
                'body'        => <<<'EOD'
Dear {{customer_name}},

Unfortunately, your payment could not be processed. Please review the details below and try again.

Invoice:        {{invoice_number}}
Amount:         {{currency}} {{amount}}
Date:           {{payment_date}}
Reason:         {{failure_reason}}

If you continue to experience issues, please contact our support team.

Best regards,
Finance Team
EOD,
                'description' => 'Sent to customer when a payment attempt fails.',
            ],

            // Payment Failed (SMS)
            [
                'code'    => 'core_payment_failed_sms',
                'channel' => 'sms',
                'name'    => 'Core: Payment Failed (SMS)',
                'subject' => null,
                'body'    => 'Payment FAILED for Invoice {{invoice_number}} ({{currency}} {{amount}}). Reason: {{failure_reason}}. Please retry or contact support.',
                'description' => 'SMS alert when a payment attempt fails.',
            ],
        ];

        foreach ($templates as $data) {
            CommTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        $this->command->info('Core comm templates seeded: ' . count($templates) . ' templates.');
    }
}
