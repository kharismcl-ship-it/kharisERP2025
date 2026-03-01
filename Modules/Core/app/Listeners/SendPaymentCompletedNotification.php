<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Core\Events\PaymentCompleted;
use Modules\Finance\Models\Invoice;

class SendPaymentCompletedNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(PaymentCompleted $event): void
    {
        $metadata      = $event->metadata;
        $paymentResult = $event->paymentResult;

        // Resolve notifiable — prefer Finance Invoice (has customer name/email context)
        $notifiable = null;
        if (! empty($metadata['invoice_id'])) {
            $notifiable = Invoice::find($metadata['invoice_id']);
        }

        if (! $notifiable) {
            return;
        }

        $amount    = $paymentResult['amount'] ?? ($notifiable->total ?? 0);
        $reference = $paymentResult['reference'] ?? ($paymentResult['transaction_id'] ?? 'N/A');

        $data = [
            'customer_name'  => $notifiable->customer_name ?? 'Valued Customer',
            'amount'         => number_format((float) $amount, 2),
            'currency'       => $paymentResult['currency'] ?? 'GHS',
            'reference'      => $reference,
            'payment_date'   => now()->format('d M Y H:i'),
            'invoice_number' => $notifiable->invoice_number ?? 'N/A',
            'module'         => ucfirst($metadata['module'] ?? 'General'),
        ];

        try {
            $this->comms->sendToModel(
                $notifiable,
                'email',
                'core_payment_completed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendPaymentCompletedNotification failed', [
                'invoice_id' => $metadata['invoice_id'] ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
