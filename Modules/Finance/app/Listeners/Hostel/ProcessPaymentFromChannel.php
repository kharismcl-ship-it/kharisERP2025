<?php

namespace Modules\Finance\Listeners\Hostel;

use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\Receipt;
use Modules\PaymentsChannel\Models\PayTransaction;

class ProcessPaymentFromChannel
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PayTransaction $transaction)
    {
        $intent = $transaction->payIntent;
        $metadata = $intent?->metadata ?? [];

        if (! empty($metadata['invoice_id'])) {
            $invoice = Invoice::find($metadata['invoice_id']);

            if ($invoice) {
                // Create a payment record
                $payment = Payment::create([
                    'company_id' => $invoice->company_id,
                    'invoice_id' => $invoice->id,
                    'amount' => $transaction->amount,
                    'payment_date' => $transaction->processed_at ?? $transaction->created_at,
                    'payment_method' => $intent?->payMethod?->code ?? $transaction->provider,
                    'reference' => $transaction->provider_transaction_id,
                ]);

                // Create receipt for the payment
                Receipt::create([
                    'company_id' => $invoice->company_id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'receipt_number' => Receipt::generateReceiptNumber(),
                    'receipt_date' => now(),
                    'customer_name' => $invoice->customer_name,
                    'customer_email' => $invoice->customer_email,
                    'customer_phone' => $invoice->customer_phone,
                    'customer_type' => $invoice->customer_type,
                    'customer_id' => $invoice->customer_id,
                    'amount' => $transaction->amount,
                    'payment_method' => $intent?->payMethod?->code ?? $transaction->provider,
                    'reference' => $transaction->provider_transaction_id,
                    'status' => 'draft',
                ]);

                // Update invoice status based on amount paid
                $totalPaid = $invoice->payments()->sum('amount');

                if ($totalPaid >= $invoice->total) {
                    $invoice->update(['status' => 'paid']);
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => 'partial']);
                }
            }
        }
    }
}
