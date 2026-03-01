<?php

namespace Modules\Sales\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Sales\Events\PosSaleCompleted;

class SendPosReceiptSms
{
    public function handle(PosSaleCompleted $event): void
    {
        $sale    = $event->sale;
        $contact = $sale->contact;

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
                templateCode: 'pos_receipt_sms',
                data: [
                    'contact_name' => $contact->full_name,
                    'receipt_ref'  => $sale->reference,
                    'total'        => number_format($sale->total, 2),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("SendPosReceiptSms failed for sale {$sale->id}: {$e->getMessage()}");
        }
    }
}