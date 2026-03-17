<?php

namespace Modules\Farms\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\FarmOrderPlaced;

class SendFarmOrderConfirmation
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(FarmOrderPlaced $event): void
    {
        $order = $event->order->load('items');

        // Require at minimum a phone number to send
        if (empty($order->customer_phone)) {
            return;
        }

        $itemLines = $order->items->map(function ($item) {
            return "{$item->product_name} × {$item->quantity} {$item->unit} @ GHS {$item->unit_price}";
        })->implode("\n");

        $data = [
            'customer_name'   => $order->customer_name,
            'order_ref'       => $order->ref,
            'item_count'      => $order->items->count(),
            'item_lines'      => $itemLines,
            'subtotal'        => number_format((float) $order->subtotal, 2),
            'delivery_fee'    => number_format((float) $order->delivery_fee, 2),
            'total'           => number_format((float) $order->total, 2),
            'delivery_type'   => $order->delivery_type === 'pickup' ? 'Farm Pickup' : 'Delivery',
            'delivery_address'=> $order->delivery_address ?? 'N/A',
            'currency'        => 'GHS',
            'order_date'      => $order->placed_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
            'track_url'       => route('farm-shop.track') . '?ref=' . $order->ref,
        ];

        try {
            // Send email if customer provided one
            if (! empty($order->customer_email)) {
                $this->comms->sendToModel($order, 'email', 'farms_order_confirmed', $data);
            }

            // Send SMS confirmation
            $this->comms->sendToModel($order, 'sms', 'farms_order_confirmed_sms', $data);

        } catch (\Throwable $e) {
            Log::warning('SendFarmOrderConfirmation failed', [
                'farm_order_id' => $order->id,
                'order_ref'     => $order->ref,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
