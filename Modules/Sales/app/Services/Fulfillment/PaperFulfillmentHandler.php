<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class PaperFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'ManufacturingPaper';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        // Decrement available pool on the production batch or grade record
        // In a real system this would integrate with batch inventory; here we mark fulfilled
        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}