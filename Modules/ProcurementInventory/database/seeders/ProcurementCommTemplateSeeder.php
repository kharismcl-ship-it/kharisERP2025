<?php

namespace Modules\ProcurementInventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class ProcurementCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // PO Approved — vendor notification (email)
            [
                'code'        => 'procurement_po_approved',
                'channel'     => 'email',
                'name'        => 'Procurement: Purchase Order Approved (Email)',
                'subject'     => 'Purchase Order {{po_number}} — Approved',
                'body'        => <<<'EOD'
Dear {{vendor_name}},

We are pleased to inform you that a Purchase Order has been approved and issued for your supply.

PO Number:      {{po_number}}
Order Date:     {{order_date}}
Expected Date:  {{expected_date}}
Total Amount:   {{currency}} {{total_amount}}
Delivery Terms: {{delivery_terms}}

{{#notes}}
Notes: {{notes}}
{{/notes}}

Please confirm receipt of this order and advise if you have any queries.

Best regards,
Procurement Department
EOD,
                'description' => 'Sent to vendor when a purchase order is approved.',
            ],

            // PO Approved — vendor notification (SMS)
            [
                'code'    => 'procurement_po_approved_sms',
                'channel' => 'sms',
                'name'    => 'Procurement: Purchase Order Approved (SMS)',
                'subject' => null,
                'body'    => 'PO {{po_number}} approved. Amount: {{currency}} {{total_amount}}. Expected delivery: {{expected_date}}. Confirm receipt.',
                'description' => 'SMS notification to vendor when a PO is approved.',
            ],

            // Low Stock Alert (email)
            [
                'code'        => 'procurement_low_stock_alert',
                'channel'     => 'email',
                'name'        => 'Procurement: Low Stock Alert (Email)',
                'subject'     => 'Low Stock Alert — {{item_name}}',
                'body'        => <<<'EOD'
Dear Procurement Team,

A stock level has fallen to or below the reorder threshold. Please arrange a purchase order.

Item:            {{item_name}} ({{item_code}})
Quantity On Hand: {{quantity_on_hand}} {{uom}}
Reorder Level:   {{reorder_level}} {{uom}}
Suggested Order: {{reorder_quantity}} {{uom}}
Alert Date:      {{alert_date}}

Please review and raise a purchase order at your earliest convenience.

Best regards,
Inventory Management System
EOD,
                'description' => 'Sent to procurement team when an item falls below reorder level.',
            ],

            // Low Stock Alert (SMS)
            [
                'code'    => 'procurement_low_stock_alert_sms',
                'channel' => 'sms',
                'name'    => 'Procurement: Low Stock Alert (SMS)',
                'subject' => null,
                'body'    => 'LOW STOCK: {{item_name}} ({{item_code}}) — {{quantity_on_hand}} {{uom}} remaining (reorder at {{reorder_level}}). Raise PO now.',
                'description' => 'SMS alert when stock falls below reorder level.',
            ],
        ];

        // Warehouse Transfer Completed (email)
        $templates[] = [
            'code'        => 'procurement_warehouse_transfer_completed',
            'channel'     => 'email',
            'name'        => 'Procurement: Warehouse Transfer Completed (Email)',
            'subject'     => 'Warehouse Transfer {{reference}} — Completed',
            'body'        => <<<'EOD'
Dear Warehouse Team,

A stock transfer has been completed successfully.

Reference:    {{reference}}
From:         {{from_warehouse}}
To:           {{to_warehouse}}
Completed At: {{completed_at}}
Total Lines:  {{total_lines}}
Total Qty:    {{total_qty}}
Approved By:  {{approved_by}}

Please update your physical records accordingly.

Best regards,
Inventory Management System
EOD,
            'description' => 'Sent when a warehouse-to-warehouse stock transfer is completed.',
        ];

        foreach ($templates as $data) {
            CommTemplate::updateOrCreate(['code' => $data['code']], $data);
        }

        $this->command->info('Procurement comm templates seeded: ' . count($templates) . ' templates.');
    }
}