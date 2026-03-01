<?php

namespace Modules\Sales\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Sales\Events\QuotationSent;

class SendQuotationEmail
{
    public function handle(QuotationSent $event): void
    {
        $quotation = $event->quotation;
        $contact   = $quotation->contact;

        if (! $contact || ! $contact->email) {
            return;
        }

        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        try {
            app(\Modules\CommunicationCentre\Services\CommunicationService::class)->sendToContact(
                channel: 'email',
                toEmail: $contact->email,
                toPhone: null,
                subject: 'Quotation ' . $quotation->reference,
                templateCode: 'sales_quotation_sent',
                data: [
                    'contact_name' => $contact->full_name,
                    'quotation_ref' => $quotation->reference,
                    'total'         => number_format($quotation->total, 2),
                    'valid_until'   => optional($quotation->valid_until)->format('d M Y'),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("SendQuotationEmail failed for quotation {$quotation->id}: {$e->getMessage()}");
        }
    }
}