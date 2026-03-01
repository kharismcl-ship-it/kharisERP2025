<?php

namespace Modules\Sales\Services\Fulfillment;

use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

class HostelsFulfillmentHandler implements FulfillmentHandlerInterface
{
    public function sourceModule(): string
    {
        return 'Hostels';
    }

    public function handle(SalesOrder $order, SalesOrderLine $line): bool
    {
        $catalogItem = $line->catalogItem;

        if ($catalogItem->source_module !== $this->sourceModule()) {
            return false;
        }

        // Create a Hostel Booking stub — the actual booking wizard assigns bed/room
        if (class_exists(\Modules\Hostels\Models\Booking::class)) {
            $contact = $order->contact;

            \Modules\Hostels\Models\Booking::create([
                'company_id'      => $order->company_id,
                'hostel_id'       => $catalogItem->source_id,
                'booking_date'    => now()->toDateString(),
                'check_in_date'   => now()->toDateString(),
                'check_out_date'  => now()->addDays(1)->toDateString(),
                'status'          => 'pending',
                'contact_name'    => optional($contact)->full_name,
                'contact_phone'   => optional($contact)->phone,
                'contact_email'   => optional($contact)->email,
                'notes'           => 'Auto-created from Sales Order ' . $order->reference,
            ]);
        }

        $line->update(['fulfilled_quantity' => $line->quantity]);

        return true;
    }
}