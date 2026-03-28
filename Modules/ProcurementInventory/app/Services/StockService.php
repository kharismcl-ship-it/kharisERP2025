<?php

namespace Modules\ProcurementInventory\Services;

use Modules\ProcurementInventory\Events\StockLevelLow;
use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Models\StockMovement;
use Modules\ProcurementInventory\Models\Warehouse;

class StockService
{
    /**
     * Get or create a StockLevel record for an item/company(/warehouse) tuple.
     * Pass $warehouseId = null for company-wide (non-warehouse) stock.
     */
    public function getOrCreate(int $companyId, int $itemId, ?int $warehouseId = null): StockLevel
    {
        return StockLevel::firstOrCreate(
            ['company_id' => $companyId, 'item_id' => $itemId, 'warehouse_id' => $warehouseId],
            ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_on_order' => 0]
        );
    }

    /**
     * Increase quantity_on_order when a PO is approved/ordered.
     * Uses destination_warehouse_id if set on the PO.
     */
    public function incrementOnOrder(PurchaseOrder $po): void
    {
        $warehouseId = $po->destination_warehouse_id;

        foreach ($po->lines as $line) {
            if (! $line->item_id) {
                continue;
            }
            $stock = $this->getOrCreate($po->company_id, $line->item_id, $warehouseId);
            $stock->increment('quantity_on_order', (float) $line->quantity);
        }
    }

    /**
     * Decrease quantity_on_order when a PO is cancelled.
     */
    public function decrementOnOrder(PurchaseOrder $po): void
    {
        $warehouseId = $po->destination_warehouse_id;

        foreach ($po->lines as $line) {
            if (! $line->item_id) {
                continue;
            }
            $stock     = $this->getOrCreate($po->company_id, $line->item_id, $warehouseId);
            $remaining = max(0, (float) $stock->quantity_on_order - (float) $line->quantity);
            $stock->update(['quantity_on_order' => $remaining]);
        }
    }

    /**
     * Update stock on hand when a GoodsReceipt is confirmed.
     * Goods go into the warehouse specified on the receipt (or no warehouse if null).
     */
    public function updateFromReceipt(GoodsReceipt $receipt): void
    {
        $warehouseId = $receipt->warehouse_id;

        foreach ($receipt->lines as $line) {
            if (! $line->item_id || (float) $line->quantity_received <= 0) {
                continue;
            }

            $stock  = $this->getOrCreate($receipt->company_id, $line->item_id, $warehouseId);
            $before = (float) $stock->quantity_on_hand;

            // Add received qty to on-hand
            $stock->increment('quantity_on_hand', (float) $line->quantity_received);
            $after = (float) $stock->fresh()->quantity_on_hand;

            // Reduce on-order by received qty (floor at 0)
            $newOnOrder = max(0, (float) $stock->fresh()->quantity_on_order - (float) $line->quantity_received);
            $stock->update(['quantity_on_order' => $newOnOrder]);

            $qtyReceived = (float) $line->quantity_received;
            $unitCost    = (float) ($line->unit_price ?? 0);

            // Log movement — tagged to destination warehouse
            StockMovement::create([
                'company_id'      => $receipt->company_id,
                'item_id'         => $line->item_id,
                'to_warehouse_id' => $warehouseId,
                'type'            => 'receipt',
                'quantity'        => $qtyReceived,
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'unit_cost'       => $unitCost,
                'total_cost'      => $qtyReceived * $unitCost,
                'reference'       => $receipt->grn_number ?? ('GRN-' . $receipt->id),
                'source_type'     => GoodsReceipt::class,
                'source_id'       => $receipt->id,
                'user_id'         => auth()->id(),
                'note'            => 'Goods received against ' . ($receipt->purchaseOrder->po_number ?? 'PO'),
            ]);

            // Recalculate WAC after receipt
            $stock->refresh();
            $stock->recalculateWac($qtyReceived, $unitCost);

            // Check reorder threshold even after receipt (stock may still be below minimum)
            $stock->refresh();
            if ($stock->needsReorder()) {
                StockLevelLow::dispatch($stock, $stock->item);
            }
        }
    }

    /**
     * Transfer stock from one warehouse to another.
     * Decrements source warehouse stock and increments destination warehouse stock.
     * Logs two StockMovements (out + in) linked by the same reference.
     *
     * @throws \Exception if source stock is insufficient
     */
    public function transfer(
        int $companyId,
        int $fromWarehouseId,
        int $toWarehouseId,
        int $itemId,
        float $qty,
        string $reference = '',
        ?int $userId = null
    ): void {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Transfer quantity must be greater than zero.');
        }

        // --- Source warehouse: deduct ---
        $fromStock  = $this->getOrCreate($companyId, $itemId, $fromWarehouseId);
        $fromBefore = (float) $fromStock->quantity_on_hand;

        if ($fromBefore < $qty) {
            $fromWarehouse = Warehouse::find($fromWarehouseId);
            throw new \Exception(
                "Insufficient stock in {$fromWarehouse?->name}: available {$fromBefore}, requested {$qty}."
            );
        }

        $fromAfter = $fromBefore - $qty;
        $fromStock->update(['quantity_on_hand' => $fromAfter]);

        StockMovement::create([
            'company_id'        => $companyId,
            'item_id'           => $itemId,
            'from_warehouse_id' => $fromWarehouseId,
            'to_warehouse_id'   => $toWarehouseId,
            'type'              => 'transfer',
            'quantity'          => -$qty,
            'quantity_before'   => $fromBefore,
            'quantity_after'    => $fromAfter,
            'reference'         => $reference,
            'user_id'           => $userId ?? auth()->id(),
            'note'              => "Transfer out to warehouse #{$toWarehouseId}",
        ]);

        // Check if source warehouse is now below reorder threshold
        $fromStock->refresh();
        if ($fromStock->needsReorder()) {
            StockLevelLow::dispatch($fromStock, $fromStock->item);
        }

        // --- Destination warehouse: add ---
        $toStock  = $this->getOrCreate($companyId, $itemId, $toWarehouseId);
        $toBefore = (float) $toStock->quantity_on_hand;
        $toAfter  = $toBefore + $qty;
        $toStock->update(['quantity_on_hand' => $toAfter]);

        StockMovement::create([
            'company_id'        => $companyId,
            'item_id'           => $itemId,
            'from_warehouse_id' => $fromWarehouseId,
            'to_warehouse_id'   => $toWarehouseId,
            'type'              => 'transfer',
            'quantity'          => $qty,
            'quantity_before'   => $toBefore,
            'quantity_after'    => $toAfter,
            'reference'         => $reference,
            'user_id'           => $userId ?? auth()->id(),
            'note'              => "Transfer in from warehouse #{$fromWarehouseId}",
        ]);
    }

    /**
     * Manually adjust stock on hand and log the movement.
     */
    public function adjust(
        int $companyId,
        int $itemId,
        float $adjustment,
        string $note = '',
        ?int $userId = null,
        ?int $warehouseId = null
    ): StockLevel {
        $stock  = $this->getOrCreate($companyId, $itemId, $warehouseId);
        $before = (float) $stock->quantity_on_hand;
        $after  = max(0, $before + $adjustment);

        $avgCost = (float) $stock->average_unit_cost;

        $stock->update([
            'quantity_on_hand' => $after,
            'last_counted_at'  => now(),
            'total_value'      => $after * $avgCost,
        ]);

        StockMovement::create([
            'company_id'      => $companyId,
            'item_id'         => $itemId,
            'to_warehouse_id' => $warehouseId,
            'type'            => 'adjustment',
            'quantity'        => $adjustment,
            'quantity_before' => $before,
            'quantity_after'  => $after,
            'unit_cost'       => $avgCost,
            'total_cost'      => abs($adjustment) * $avgCost,
            'user_id'         => $userId ?? auth()->id(),
            'note'            => $note,
        ]);

        $fresh = $stock->fresh();

        // Alert if stock has dropped below reorder threshold
        if ($adjustment < 0 && $fresh->needsReorder()) {
            StockLevelLow::dispatch($fresh, $fresh->item);
        }

        return $fresh;
    }
}