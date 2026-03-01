<?php

namespace Modules\Finance\Listeners\Finance;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Finance\Events\InvoiceCreated;

class SendInvoiceNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        // Only notify for customer-facing invoices
        if ($invoice->type !== 'customer') {
            return;
        }

        $data = [
            'invoice_number'  => $invoice->invoice_number,
            'customer_name'   => $invoice->customer_name ?? 'Valued Customer',
            'invoice_date'    => $invoice->invoice_date?->format('d M Y'),
            'due_date'        => $invoice->due_date?->format('d M Y'),
            'total'           => number_format((float) $invoice->total, 2),
            'currency'        => 'GHS',
        ];

        try {
            // Send via all enabled channels using the invoice itself as the notifiable context.
            // Falls back gracefully if no channel is configured for this company.
            $this->comms->sendToModel(
                $invoice,
                'email',
                'finance_invoice_issued',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendInvoiceNotification failed', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}