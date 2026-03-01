<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class FleetFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'Fleet';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        // Create a TripLog booking record
        if (class_exists(\Modules\Fleet\Models\TripLog::class)) {
            $contact = $order->contact;

            \Modules\Fleet\Models\TripLog::create([
                'company_id'    => $order->company_id,
                'vehicle_id'    => $catalogItem->source_id,
                'trip_date'     => now()->toDateString(),
                'destination'   => 'Sales Order ' . $order->reference,
                'purpose'       => 'Booked via Sales',
                'fare_amount'   => $line->line_total,
                'client_name'   => optional($contact)->full_name,
                'client_phone'  => optional($contact)->phone,
                'client_email'  => optional($contact)->email,
                'status'        => 'planned',
            ]);
        }

        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}