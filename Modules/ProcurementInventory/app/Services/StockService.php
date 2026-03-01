<?php

namespace Modules\ProcurementInventory\Services;

use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\StockLevel;

class StockService
{
    /**
     * Get or create a StockLevel record for an item/company pair.
     */
    public function getOrCreate(int $companyId, int $itemId): StockLevel
    {
        return StockLevel::firstOrCreate(
            ['company_id' => $companyId, 'item_id' => $itemId],
            ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_on_order' => 0]
        );
    }

    /**
     * Increase quantity_on_order when a PO is approved/ordered.
     */
    public function incrementOnOrder(PurchaseOrder $po): void
    {
        foreach ($po->lines as $line) {
            if (! $line->item_id) {
                continue;
            }
            $stock = $this->getOrCreate($po->company_id, $line->item_id);
            $stock->increment('quantity_on_order', (float) $line->quantity);
        }
    }

    /**
     * Decrease quantity_on_order when a PO is cancelled.
     */
    public function decrementOnOrder(PurchaseOrder $po): void
    {
        foreach ($po->lines as $line) {
            if (! $line->item_id) {
                continue;
            }
            $stock = $this->getOrCreate($po->company_id, $line->item_id);
            $remaining = max(0, (float) $stock->quantity_on_order - (float) $line->quantity);
            $stock->update(['quantity_on_order' => $remaining]);
        }
    }

    /**
     * Update stock on hand when a GoodsReceipt is confirmed.
     * Also reduces quantity_on_order by the received amount.
     */
    public function updateFromReceipt(GoodsReceipt $receipt): void
    {
        foreach ($receipt->lines as $line) {
            if (! $line->item_id || (float) $line->quantity_received <= 0) {
                continue;
            }

            $stock = $this->getOrCreate($receipt->company_id, $line->item_id);

            // Add received qty to on-hand
            $stock->increment('quantity_on_hand', (float) $line->quantity_received);

            // Reduce on-order by received qty (floor at 0)
            $newOnOrder = max(0, (float) $stock->fresh()->quantity_on_order - (float) $line->quantity_received);
            $stock->update(['quantity_on_order' => $newOnOrder]);
        }
    }
}
