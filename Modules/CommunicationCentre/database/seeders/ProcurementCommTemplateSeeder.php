<?php

namespace Modules\CommunicationCentre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class ProcurementCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // PO Submitted — notify approvers
            [
                'code'        => 'po_submitted_approval',
                'channel'     => 'email',
                'name'        => 'PO Submitted for Approval',
                'subject'     => 'Purchase Order {{po_number}} Awaiting Approval',
                'body'        => <<<'EOD'
Dear {{approver_name}},

A purchase order has been submitted and requires your approval.

Details:
- PO Number: {{po_number}}
- Vendor: {{vendor_name}}
- Total Amount: {{currency}} {{total_amount}}
- Requested by: {{requester_name}}
- Date: {{po_date}}

Please log in to the system to review and approve or reject this purchase order.

Best regards,
Procurement System
EOD,
                'description' => 'Sent to approvers when a PO is submitted for approval',
            ],

            // PO Approved — notify vendor contact / purchasing team
            [
                'code'        => 'po_approved_vendor',
                'channel'     => 'email',
                'name'        => 'PO Approved — Vendor Notification',
                'subject'     => 'Purchase Order {{po_number}} Approved',
                'body'        => <<<'EOD'
Dear {{vendor_name}},

We are pleased to inform you that Purchase Order {{po_number}} has been approved.

Order Details:
- PO Number: {{po_number}}
- PO Date: {{po_date}}
- Expected Delivery: {{expected_delivery_date}}
- Total Amount: {{currency}} {{total_amount}}

Please arrange delivery to: {{delivery_address}}

Kindly confirm receipt of this order.

Best regards,
{{company_name}}
Procurement Department
EOD,
                'description' => 'Sent when a PO is approved; can be forwarded to vendor',
            ],

            // GRN Confirmed — internal notification
            [
                'code'        => 'grn_confirmed',
                'channel'     => 'email',
                'name'        => 'Goods Receipt Confirmed',
                'subject'     => 'Goods Received — {{grn_number}}',
                'body'        => <<<'EOD'
Dear {{recipient_name}},

Goods have been received and confirmed against Purchase Order {{po_number}}.

GRN Details:
- GRN Number: {{grn_number}}
- Received Date: {{received_date}}
- Vendor: {{vendor_name}}
- Received by: {{received_by}}

Stock levels have been updated accordingly. Please proceed with payment processing for the corresponding invoice.

Best regards,
Procurement System
EOD,
                'description' => 'Internal notification when a goods receipt is confirmed',
            ],

            // Stock Low Alert
            [
                'code'        => 'stock_low_alert',
                'channel'     => 'email',
                'name'        => 'Low Stock Alert',
                'subject'     => 'Low Stock Alert: {{item_name}} ({{sku}})',
                'body'        => <<<'EOD'
Dear {{recipient_name}},

The following item has fallen below its reorder level and requires attention:

Item: {{item_name}}
SKU: {{sku}}
Current Stock: {{quantity_on_hand}} {{unit_of_measure}}
Reorder Level: {{reorder_level}} {{unit_of_measure}}
Suggested Reorder Qty: {{reorder_quantity}} {{unit_of_measure}}

Please create a Purchase Order to replenish this item.

Best regards,
Inventory Management System
EOD,
                'description' => 'Alert sent when stock falls below reorder level',
            ],

            // SMS version of low stock alert
            [
                'code'        => 'stock_low_alert_sms',
                'channel'     => 'sms',
                'name'        => 'Low Stock Alert SMS',
                'subject'     => null,
                'body'        => 'LOW STOCK: {{item_name}} ({{sku}}) - On Hand: {{quantity_on_hand}}, Reorder Level: {{reorder_level}}. Please create a PO.',
                'description' => 'SMS alert for low stock',
            ],

            // PO Cancelled
            [
                'code'        => 'po_cancelled',
                'channel'     => 'email',
                'name'        => 'PO Cancelled',
                'subject'     => 'Purchase Order {{po_number}} Cancelled',
                'body'        => <<<'EOD'
Dear {{recipient_name}},

Purchase Order {{po_number}} has been cancelled.

Details:
- PO Number: {{po_number}}
- Vendor: {{vendor_name}}
- Total Amount: {{currency}} {{total_amount}}
- Cancelled by: {{cancelled_by}}
- Date: {{cancelled_date}}

If this cancellation was made in error, please contact the procurement team immediately.

Best regards,
Procurement System
EOD,
                'description' => 'Notification sent when a PO is cancelled',
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
