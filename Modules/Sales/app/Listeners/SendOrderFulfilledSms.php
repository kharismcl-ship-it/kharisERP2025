<?php

namespace Modules\Sales\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Sales\Events\SalesOrderFulfilled;

class SendOrderFulfilledSms
{
    public function handle(SalesOrderFulfilled $event): void
    {
        $order   = $event->order;
        $contact = $order->contact;

        if (! $contact || ! $contact->phone) {
            return;
        }

        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        try {
            app(\Modules\CommunicationCentre\Services\CommunicationService::class)->sendToContact(
                channel: 'sms',
                toEmail: null,
                toPhone: $contact->phone,
                subject: null,
                templateCode: 'sales_order_fulfilled',
                data: [
                    'contact_name' => $contact->full_name,
                    'order_ref'    => $order->reference,
                    'total'        => number_format($order->total, 2),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("SendOrderFulfilledSms failed for order {$order->id}: {$e->getMessage()}");
        }
    }
}