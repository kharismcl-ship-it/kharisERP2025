<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class SalesCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'     => 'sales_quotation_sent',
                'name'     => 'Quotation Sent',
                'channel'  => 'email',
                'subject'  => 'Your Quotation {{quotation_ref}}',
                'body'     => "Dear {{contact_name}},\n\nPlease find attached your quotation {{quotation_ref}} totalling GHS {{total}}.\n\nThis quotation is valid until {{valid_until}}.\n\nThank you for your interest.",
                'is_active' => true,
            ],
            [
                'code'     => 'sales_order_confirmed',
                'name'     => 'Order Confirmed (Email)',
                'channel'  => 'email',
                'subject'  => 'Sales Order {{order_ref}} Confirmed',
                'body'     => "Dear {{contact_name}},\n\nYour order {{order_ref}} totalling GHS {{total}} has been confirmed and is being processed.\n\nThank you for your business.",
                'is_active' => true,
            ],
            [
                'code'     => 'sales_order_confirmed_sms',
                'name'     => 'Order Confirmed (SMS)',
                'channel'  => 'sms',
                'subject'  => null,
                'body'     => 'Your order {{order_ref}} (GHS {{total}}) is confirmed. Thank you!',
                'is_active' => true,
            ],
            [
                'code'     => 'sales_order_fulfilled',
                'name'     => 'Order Fulfilled (SMS)',
                'channel'  => 'sms',
                'subject'  => null,
                'body'     => 'Hi {{contact_name}}, your order {{order_ref}} has been delivered. Thank you for choosing us!',
                'is_active' => true,
            ],
            [
                'code'     => 'pos_receipt_sms',
                'name'     => 'POS Receipt (SMS)',
                'channel'  => 'sms',
                'subject'  => null,
                'body'     => 'Receipt {{receipt_ref}}: GHS {{total}}. Thank you for your purchase!',
                'is_active' => true,
            ],
            [
                'code'     => 'sales_lead_assigned',
                'name'     => 'Lead Assigned (DB Notification)',
                'channel'  => 'database',
                'subject'  => 'New Lead Assigned',
                'body'     => 'You have been assigned a new lead: {{lead_name}}.',
                'is_active' => true,
            ],
            [
                'code'     => 'restaurant_order_ready',
                'name'     => 'Restaurant Order Ready (DB)',
                'channel'  => 'database',
                'subject'  => 'Order Ready',
                'body'     => 'Table {{table_number}}: order is ready to be served.',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $data) {
            CommTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        $this->command->info('SalesCommTemplateSeeder: ' . count($templates) . ' templates seeded.');
    }
}
