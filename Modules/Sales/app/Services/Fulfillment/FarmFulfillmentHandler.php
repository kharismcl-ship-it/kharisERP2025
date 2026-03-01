<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class FarmFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'Farms';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        // Decrement farm inventory quantity if model exists
        if ($catalogItem->source_id && class_exists(\Modules\Farms\Models\FarmInventory::class)) {
            $inventory = \Modules\Farms\Models\FarmInventory::find($catalogItem->source_id);
            if ($inventory && $inventory->quantity >= $line->quantity) {
                $inventory->decrement('quantity', $line->quantity);
            }
        }

        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}