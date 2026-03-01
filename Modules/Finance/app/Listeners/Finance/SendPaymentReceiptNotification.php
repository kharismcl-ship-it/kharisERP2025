<?php

namespace Modules\Finance\Listeners\Finance;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Finance\Events\PaymentReceiptReady;

class SendPaymentReceiptNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(PaymentReceiptReady $event): void
    {
        $payment = $event->payment;
        $invoice = $event->invoice;

        $data = [
            'invoice_number'   => $invoice->invoice_number,
            'customer_name'    => $invoice->customer_name ?? 'Valued Customer',
            'payment_date'     => $payment->payment_date?->format('d M Y') ?? now()->format('d M Y'),
            'amount_paid'      => number_format((float) $payment->amount, 2),
            'invoice_total'    => number_format((float) $invoice->total, 2),
            'currency'         => 'GHS',
            'payment_method'   => $payment->method ?? 'N/A',
            'reference'        => $payment->reference ?? $payment->id,
        ];

        try {
            $this->comms->sendToModel(
                $invoice,
                'email',
                'finance_payment_receipt',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendPaymentReceiptNotification failed', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}