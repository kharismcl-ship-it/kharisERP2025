<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class FinanceCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Payment Receipt — email
            [
                'code'        => 'payment_receipt',
                'channel'     => 'email',
                'name'        => 'Payment Receipt',
                'subject'     => 'Payment Receipt - {{receipt_number}}',
                'body'        => <<<'EOD'
Dear {{name}},

Thank you for your payment. Please find your receipt details below.

Receipt Number: {{receipt_number}}
Reference:      {{booking_reference}}
Amount Paid:    GHS {{amount}}
Date:           {{date}}
Payment Method: {{payment_method}}

If you have any questions about this receipt, please contact us.

Best regards,
{{company_name}}
EOD,
                'description' => 'Email sent to customer with payment receipt details',
            ],

            // Payment Receipt — SMS
            [
                'code'        => 'payment_receipt_sms',
                'channel'     => 'sms',
                'name'        => 'Payment Receipt SMS',
                'subject'     => null,
                'body'        => 'Hi {{name}}, payment of GHS {{amount}} received on {{date}}. Receipt: {{receipt_number}}. Thank you - {{company_name}}.',
                'description' => 'SMS confirmation sent to customer after payment',
            ],

            // Invoice Sent — email
            [
                'code'        => 'invoice_sent',
                'channel'     => 'email',
                'name'        => 'Invoice Sent',
                'subject'     => 'Invoice {{invoice_number}} from {{company_name}}',
                'body'        => <<<'EOD'
Dear {{name}},

Please find your invoice attached.

Invoice Number: {{invoice_number}}
Invoice Date:   {{invoice_date}}
Due Date:       {{due_date}}
Amount Due:     GHS {{amount}}

Please ensure payment is made before the due date to avoid any late fees.

If you have any questions, please do not hesitate to contact us.

Best regards,
{{company_name}}
EOD,
                'description' => 'Email sent to customer when an invoice is issued',
            ],

            // Payment Reminder — email
            [
                'code'        => 'payment_reminder',
                'channel'     => 'email',
                'name'        => 'Payment Reminder',
                'subject'     => 'Payment Reminder — Invoice {{invoice_number}} is Due',
                'body'        => <<<'EOD'
Dear {{name}},

This is a friendly reminder that the following invoice is due{{days_overdue_text}}.

Invoice Number: {{invoice_number}}
Amount Due:     GHS {{amount}}
Due Date:       {{due_date}}

Please arrange payment as soon as possible to avoid any service interruptions.

If you have already made payment, please disregard this notice.

Best regards,
{{company_name}}
EOD,
                'description' => 'Reminder email for overdue or upcoming invoice payments',
            ],

            // Payment Reminder — SMS
            [
                'code'        => 'payment_reminder_sms',
                'channel'     => 'sms',
                'name'        => 'Payment Reminder SMS',
                'subject'     => null,
                'body'        => 'Hi {{name}}, invoice {{invoice_number}} of GHS {{amount}} is due on {{due_date}}. Please pay promptly. {{company_name}}.',
                'description' => 'SMS reminder for overdue or upcoming invoice payments',
            ],

            // Payment Confirmed — SMS
            [
                'code'        => 'payment_confirmed',
                'channel'     => 'sms',
                'name'        => 'Payment Confirmed SMS',
                'subject'     => null,
                'body'        => 'Hi {{name}}, payment of GHS {{amount}} for invoice {{invoice_number}} confirmed. Ref: {{reference}}. Thank you - {{company_name}}.',
                'description' => 'SMS sent to customer confirming payment received against an invoice',
            ],

            // Payment Confirmed — WhatsApp
            [
                'code'        => 'payment_confirmed_whatsapp',
                'channel'     => 'whatsapp',
                'name'        => 'Payment Confirmed WhatsApp',
                'subject'     => null,
                'body'        => "Payment Confirmed\n\nHi {{name}}, your payment of GHS {{amount}} has been received.\n\nInvoice: {{invoice_number}}\nReference: {{reference}}\nDate: {{date}}\n\nThank you for your payment.\n{{company_name}}",
                'description' => 'WhatsApp confirmation sent to customer after payment',
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
