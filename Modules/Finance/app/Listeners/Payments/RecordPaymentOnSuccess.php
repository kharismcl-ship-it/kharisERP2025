<?php

namespace Modules\Finance\Listeners\Payments;

use Modules\Finance\Services\IntegrationService;
use Modules\PaymentsChannel\Events\PaymentSucceeded;

class RecordPaymentOnSuccess
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PaymentSucceeded $event)
    {
        $intent = $event->payIntent;

        // Ensure we have an invoice linked in intent metadata
        $metadata = $intent->metadata ?? [];
        if (empty($metadata['invoice_id'])) {
            return;
        }

        // Find latest successful transaction for this intent
        $transaction = $intent->transactions()
            ->where('status', 'successful')
            ->latest()
            ->first();

        if (! $transaction) {
            return;
        }

        // Delegate processing to IntegrationService
        app(IntegrationService::class)->processPaymentTransaction($transaction);
    }
}
