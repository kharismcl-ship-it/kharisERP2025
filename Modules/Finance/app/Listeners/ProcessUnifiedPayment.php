<?php

namespace Modules\Finance\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Core\Events\PaymentCompleted;
use Modules\Finance\Services\IntegrationService;

class ProcessUnifiedPayment
{
    protected $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    /**
     * Handle the PaymentCompleted event
     *
     * @return void
     */
    public function handle(PaymentCompleted $event)
    {
        try {
            $paymentData = [
                'module' => $event->module,
                'entity_type' => $event->entityType,
                'entity_id' => $event->entityId,
                'amount' => $event->amount,
                'payment_method' => $event->paymentMethod,
                'transaction_id' => $event->transactionId,
            ];

            $payment = $this->integrationService->processPaymentFromEvent($paymentData);

            if ($payment) {
                Log::info('Payment processed successfully through unified system', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                    'module' => $event->module,
                    'entity_type' => $event->entityType,
                    'entity_id' => $event->entityId,
                ]);
            } else {
                Log::warning('Payment processing failed - no invoice found', [
                    'module' => $event->module,
                    'entity_type' => $event->entityType,
                    'entity_id' => $event->entityId,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process unified payment: '.$e->getMessage(), [
                'event_data' => [
                    'module' => $event->module,
                    'entity_type' => $event->entityType,
                    'entity_id' => $event->entityId,
                    'amount' => $event->amount,
                ],
                'exception' => $e,
            ]);
        }
    }
}
