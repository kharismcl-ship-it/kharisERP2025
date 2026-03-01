<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class WaterFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'ManufacturingWater';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        if (class_exists(\Modules\ManufacturingWater\Models\MwDistributionRecord::class)) {
            $contact = $order->contact;

            \Modules\ManufacturingWater\Models\MwDistributionRecord::create([
                'company_id'     => $order->company_id,
                'plant_id'       => $catalogItem->source_id,
                'distribution_date' => now()->toDateString(),
                'destination'    => optional($contact)->full_name ?? 'Sales Order ' . $order->reference,
                'volume_liters'  => $line->quantity,
                'unit_price'     => $line->unit_price,
                'customer_name'  => optional($contact)->full_name,
                'customer_phone' => optional($contact)->phone,
                'customer_email' => optional($contact)->email,
                'notes'          => 'Auto-created from Sales Order ' . $order->reference,
            ]);
        }

        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}