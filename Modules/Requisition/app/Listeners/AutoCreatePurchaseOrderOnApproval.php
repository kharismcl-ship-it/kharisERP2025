<?php

namespace Modules\Requisition\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\PurchaseOrderLine;
use Modules\Requisition\Events\RequisitionStatusChanged;
use Modules\Requisition\Models\RequisitionActivity;

class AutoCreatePurchaseOrderOnApproval
{
    public function handle(RequisitionStatusChanged $event): void
    {
        $requisition = $event->requisition;

        // Only trigger for material/equipment types on approval, with a preferred vendor set
        if ($requisition->status !== 'approved') {
            return;
        }

        if (! in_array($requisition->request_type, ['material', 'equipment'])) {
            return;
        }

        if (! $requisition->preferred_vendor_id) {
            // Log that a vendor is needed for PO creation
            RequisitionActivity::log(
                $requisition,
                'status_changed',
                'No preferred vendor set — Purchase Order creation skipped. Assign a preferred vendor and create PO manually.',
            );
            return;
        }

        // Only create PO for items linked to procurement catalog
        $linkedItems = $requisition->items()->whereNotNull('item_id')->get();

        if ($linkedItems->isEmpty()) {
            return;
        }

        try {
            $po = PurchaseOrder::create([
                'company_id'  => $requisition->company_id,
                'vendor_id'   => $requisition->preferred_vendor_id,
                'po_number'   => 'PO-' . now()->format('Ym') . '-' . str_pad(PurchaseOrder::count() + 1, 5, '0', STR_PAD_LEFT),
                'po_date'     => now()->toDateString(),
                'status'      => 'draft',
                'notes'       => "Auto-created from Requisition {$requisition->reference}.",
                'currency'    => 'GHS',
            ]);

            foreach ($linkedItems as $item) {
                PurchaseOrderLine::create([
                    'purchase_order_id' => $po->id,
                    'item_id'           => $item->item_id,
                    'description'       => $item->description,
                    'quantity'          => $item->quantity,
                    'unit_of_measure'   => $item->unit,
                    'unit_price'        => $item->vendor_unit_price ?? $item->unit_cost ?? 0,
                ]);
            }

            $po->recalculateTotals();

            RequisitionActivity::log(
                $requisition,
                'status_changed',
                "Purchase Order {$po->po_number} auto-created as draft from this requisition.",
            );
        } catch (\Throwable $e) {
            Log::warning("[Requisition] AutoCreatePO failed for {$requisition->reference}: {$e->getMessage()}");
        }
    }
}