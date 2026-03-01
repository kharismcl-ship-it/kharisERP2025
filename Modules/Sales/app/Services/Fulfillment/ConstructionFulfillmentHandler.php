<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class ConstructionFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'Construction';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        // Create a ConstructionProject linked to this sales order
        if (class_exists(\Modules\Construction\Models\ConstructionProject::class)) {
            $contact = $order->contact;

            \Modules\Construction\Models\ConstructionProject::create([
                'company_id'   => $order->company_id,
                'name'         => $catalogItem->name . ' — ' . $order->reference,
                'client_name'  => optional($contact)->full_name ?? $order->reference,
                'budget'       => $line->line_total,
                'status'       => 'pending',
            ]);
        }

        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}