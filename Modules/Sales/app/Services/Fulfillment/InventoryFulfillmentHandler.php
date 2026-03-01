<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class InventoryFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'ProcurementInventory';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        // Adjust stock level downward if StockLevel model exists
        if ($catalogItem->source_id && class_exists(\Modules\ProcurementInventory\Models\StockLevel::class)) {
            \Modules\ProcurementInventory\Models\StockLevel::where('item_id', $catalogItem->source_id)
                ->where('company_id', $order->company_id)
                ->decrement('quantity_on_hand', $line->quantity);
        }

        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}