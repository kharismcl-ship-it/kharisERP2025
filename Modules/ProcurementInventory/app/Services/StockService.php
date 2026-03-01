<?php

namespace Modules\ProcurementInventory\Services;

use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Models\StockMovement;

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
     * Also reduces quantity_on_order by the received amount and logs a movement.
     */
    public function updateFromReceipt(GoodsReceipt $receipt): void
    {
        foreach ($receipt->lines as $line) {
            if (! $line->item_id || (float) $line->quantity_received <= 0) {
                continue;
            }

            $stock = $this->getOrCreate($receipt->company_id, $line->item_id);
            $before = (float) $stock->quantity_on_hand;

            // Add received qty to on-hand
            $stock->increment('quantity_on_hand', (float) $line->quantity_received);
            $after = (float) $stock->fresh()->quantity_on_hand;

            // Reduce on-order by received qty (floor at 0)
            $newOnOrder = max(0, (float) $stock->fresh()->quantity_on_order - (float) $line->quantity_received);
            $stock->update(['quantity_on_order' => $newOnOrder]);

            // Log movement
            StockMovement::create([
                'company_id'      => $receipt->company_id,
                'item_id'         => $line->item_id,
                'type'            => 'receipt',
                'quantity'        => (float) $line->quantity_received,
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'reference'       => $receipt->grn_number ?? ('GRN-' . $receipt->id),
                'source_type'     => GoodsReceipt::class,
                'source_id'       => $receipt->id,
                'user_id'         => auth()->id(),
                'note'            => 'Goods received against ' . ($receipt->purchaseOrder->po_number ?? 'PO'),
            ]);
        }
    }

    /**
     * Manually adjust stock on hand and log the movement.
     */
    public function adjust(int $companyId, int $itemId, float $adjustment, string $note = '', ?int $userId = null): StockLevel
    {
        $stock  = $this->getOrCreate($companyId, $itemId);
        $before = (float) $stock->quantity_on_hand;
        $after  = max(0, $before + $adjustment);

        $stock->update([
            'quantity_on_hand' => $after,
            'last_counted_at'  => now(),
        ]);

        StockMovement::create([
            'company_id'      => $companyId,
            'item_id'         => $itemId,
            'type'            => 'adjustment',
            'quantity'        => $adjustment,
            'quantity_before' => $before,
            'quantity_after'  => $after,
            'user_id'         => $userId ?? auth()->id(),
            'note'            => $note,
        ]);

        return $stock->fresh();
    }
}
