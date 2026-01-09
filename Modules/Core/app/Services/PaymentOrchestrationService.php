<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;
use Modules\Core\Events\PaymentCompleted;
use Modules\Core\Events\PaymentFailed;
use Modules\Core\Events\PaymentInitiated;

class PaymentOrchestrationService
{
    protected $paymentsService;

    protected $financeService;

    public function __construct(
        \Modules\PaymentsChannel\Services\PaymentService $paymentsService,
        \Modules\Finance\Services\IntegrationService $financeService
    ) {
        $this->paymentsService = $paymentsService;
        $this->financeService = $financeService;
    }

    /**
     * Process a booking payment through the unified payment flow
     *
     * @param  mixed  $booking
     * @return array
     */
    public function processBookingPayment($booking, array $paymentData = [])
    {
        try {
            // 1. Create invoice in Finance module
            $invoice = $this->financeService->createInvoiceForBooking($booking);

            // 2. Create payment intent and initialize payment through PaymentsChannel
            $paymentIntent = $this->paymentsService->createIntentForModel(
                $booking,
                null, // provider (use default)
                [
                    'amount' => $booking->total_amount,
                    'currency' => 'GHS',
                    'method_code' => $paymentData['payment_method'] ?? null,
                    'return_url' => $paymentData['return_url'] ?? null,
                    'callback_url' => $paymentData['callback_url'] ?? null,
                    'metadata' => [
                        'invoice_id' => $invoice->id,
                        'booking_reference' => $booking->booking_reference,
                        'module' => 'hostels',
                        'entity_type' => 'booking',
                        'entity_id' => $booking->id,
                    ],
                ]
            );

            // Check if this is an offline payment method
            $isOfflinePayment = false;
            if ($paymentIntent->payMethod && $paymentIntent->payMethod->payment_mode === 'offline') {
                $isOfflinePayment = true;
            }

            // Handle offline payments differently - no redirect to payment gateway
            if ($isOfflinePayment) {
                // For offline payments, mark as pending and return success without redirect
                $paymentIntent->update([
                    'status' => 'pending_offline',
                    'provider_reference' => 'OFFLINE-'.$paymentIntent->reference,
                ]);

                $paymentInitResponse = new \Modules\PaymentsChannel\Services\Gateway\PaymentInitResponse(
                    redirect_url: null, // No redirect for offline payments
                    reference: $paymentIntent->reference,
                    provider_reference: 'OFFLINE-'.$paymentIntent->reference,
                    extra_data: ['message' => 'Offline payment initiated. Please complete the payment manually.']
                );
            } else {
                // For online payments, initialize through payment gateway
                $paymentInitResponse = $this->paymentsService->initialize($paymentIntent);
            }

            // 3. Fire payment initiated event
            event(new PaymentInitiated($paymentIntent, [
                'module' => 'hostels',
                'entity_type' => 'booking',
                'entity_id' => $booking->id,
                'invoice_id' => $invoice->id,
            ]));

            // 4. Return unified response
            return [
                'invoice' => $invoice,
                'payment_intent' => $paymentIntent,
                'checkout_url' => $paymentInitResponse->redirect_url,
                'success' => true,
            ];

        } catch (\Exception $e) {
            Log::error('Payment orchestration failed for booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            // Fire payment failed event
            event(new PaymentFailed(null, [
                'module' => 'hostels',
                'entity_type' => 'booking',
                'entity_id' => $booking->id,
                'error' => $e->getMessage(),
            ]));

            throw new \Exception('Payment processing failed: '.$e->getMessage());
        }
    }

    /**
     * Handle payment completion from webhook or return URL
     *
     * @return array
     */
    public function handlePaymentCompletion(string $paymentReference, array $paymentData = [])
    {
        try {
            // 1. Find PayIntent by reference and verify payment
            $payIntent = \Modules\PaymentsChannel\Models\PayIntent::where('reference', $paymentReference)->first();

            if (! $payIntent) {
                throw new \Exception("Payment intent not found for reference: $paymentReference");
            }

            $paymentResult = $this->paymentsService->verify($payIntent, $paymentData);

            if ($paymentResult['success']) {
                // 2. Extract module and entity information from metadata
                $metadata = $paymentResult['metadata'] ?? [];
                $module = $metadata['module'] ?? null;
                $entityType = $metadata['entity_type'] ?? null;
                $entityId = $metadata['entity_id'] ?? null;
                $invoiceId = $metadata['invoice_id'] ?? null;

                // 3. Update Finance module with payment
                if ($invoiceId) {
                    $this->financeService->recordPayment($invoiceId, [
                        'amount' => $paymentResult['amount'],
                        'payment_method' => $paymentResult['payment_method'],
                        'reference' => $paymentReference,
                        'metadata' => $paymentResult,
                    ]);
                }

                // 4. Fire payment completed event
                event(new PaymentCompleted($paymentResult, [
                    'module' => $module,
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'invoice_id' => $invoiceId,
                ]));

                return [
                    'success' => true,
                    'message' => 'Payment completed successfully',
                    'data' => $paymentResult,
                ];
            } else {
                // Fire payment failed event
                event(new PaymentFailed($paymentReference, [
                    'module' => $metadata['module'] ?? null,
                    'entity_type' => $metadata['entity_type'] ?? null,
                    'entity_id' => $metadata['entity_id'] ?? null,
                    'error' => $paymentResult['message'] ?? 'Payment verification failed',
                ]));

                return [
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Payment verification failed',
                ];
            }

        } catch (\Exception $e) {
            Log::error('Payment completion handling failed', [
                'reference' => $paymentReference,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Payment completion failed: '.$e->getMessage());
        }
    }

    /**
     * Get payment status for a specific entity
     *
     * @return array
     */
    public function getPaymentStatus(string $module, string $entityType, int $entityId)
    {
        try {
            // 1. Find invoice through Finance module
            $invoice = $this->financeService->findInvoiceByModuleEntity($module, $entityType, $entityId);

            if (! $invoice) {
                return [
                    'status' => 'no_invoice',
                    'message' => 'No invoice found for this entity',
                ];
            }

            // 2. Get payment status from invoice
            return [
                'status' => $invoice->payment_status,
                'amount_paid' => $invoice->amount_paid,
                'amount_due' => $invoice->amount_due,
                'invoice' => $invoice,
            ];

        } catch (\Exception $e) {
            Log::error('Payment status retrieval failed', [
                'module' => $module,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Payment status retrieval failed: '.$e->getMessage());
        }
    }
}
