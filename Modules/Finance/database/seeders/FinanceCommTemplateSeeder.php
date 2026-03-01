<?php

namespace Modules\Finance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class FinanceCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'finance_invoice_issued',
                'name'    => 'Finance — Invoice Issued',
                'channel' => 'email',
                'subject' => 'Invoice {{invoice_number}} — {{currency}} {{total}}',
                'body'    => "Dear {{customer_name}},\n\nPlease find your invoice details below:\n\nInvoice No.: {{invoice_number}}\nInvoice Date: {{invoice_date}}\nDue Date: {{due_date}}\nAmount Due: {{currency}} {{total}}\n\nPlease ensure payment is made by the due date to avoid late charges.\n\nThank you for your business.",
            ],
            [
                'code'    => 'finance_invoice_overdue',
                'name'    => 'Finance — Invoice Overdue Reminder',
                'channel' => 'email',
                'subject' => 'OVERDUE: Invoice {{invoice_number}} — {{days_overdue}} days past due',
                'body'    => "Dear {{customer_name}},\n\nThis is a reminder that Invoice {{invoice_number}} is now {{days_overdue}} day(s) overdue.\n\nDue Date: {{due_date}}\nOutstanding Amount: {{currency}} {{total}}\n\nPlease arrange payment immediately to avoid further action. If you have already paid, please disregard this notice and send us your payment reference.\n\nThank you.",
            ],
            [
                'code'    => 'finance_payment_receipt',
                'name'    => 'Finance — Payment Receipt',
                'channel' => 'email',
                'subject' => 'Payment Received — Invoice {{invoice_number}}',
                'body'    => "Dear {{customer_name}},\n\nThank you! We have received your payment.\n\nInvoice No.: {{invoice_number}}\nPayment Date: {{payment_date}}\nAmount Paid: {{currency}} {{amount_paid}}\nInvoice Total: {{currency}} {{invoice_total}}\nPayment Method: {{payment_method}}\nReference: {{reference}}\n\nThis serves as your official payment receipt. Please retain for your records.\n\nThank you for your prompt payment.",
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
