<?php

namespace Modules\Sales\Listeners;

use Modules\Sales\Events\DiningOrderSentToKitchen;
use Modules\Sales\Models\KitchenTicket;

class CreateKitchenTickets
{
    public function handle(DiningOrderSentToKitchen $event): void
    {
        $order = $event->order;

        // Create one ticket per item (grouped by station if catalog item has a station tag)
        foreach ($order->items as $item) {
            if (in_array($item->status, ['cancelled', 'served'])) {
                continue;
            }

            KitchenTicket::firstOrCreate(
                [
                    'dining_order_id' => $order->id,
                    'station'         => null, // default station — can be refined
                ],
                [
                    'status'   => 'pending',
                    'fired_at' => now(),
                ]
            );
        }
    }
}