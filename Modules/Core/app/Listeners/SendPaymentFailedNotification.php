<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Core\Events\PaymentFailed;
use Modules\Finance\Models\Invoice;

class SendPaymentFailedNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(PaymentFailed $event): void
    {
        $metadata = $event->metadata;

        $notifiable = null;
        if (! empty($metadata['invoice_id'])) {
            $notifiable = Invoice::find($metadata['invoice_id']);
        }

        if (! $notifiable) {
            return;
        }

        $data = [
            'customer_name'  => $notifiable->customer_name ?? 'Valued Customer',
            'amount'         => number_format((float) ($notifiable->total ?? 0), 2),
            'currency'       => 'GHS',
            'invoice_number' => $notifiable->invoice_number ?? 'N/A',
            'failure_reason' => $metadata['error'] ?? 'Payment could not be processed.',
            'payment_date'   => now()->format('d M Y H:i'),
            'module'         => ucfirst($metadata['module'] ?? 'General'),
        ];

        try {
            $this->comms->sendToModel(
                $notifiable,
                'email',
                'core_payment_failed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendPaymentFailedNotification failed', [
                'invoice_id' => $metadata['invoice_id'] ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
