<?php

namespace Modules\Farms\Listeners;

use Modules\Farms\Models\FarmSale;
use Modules\PaymentsChannel\Events\PaymentSucceeded;

class FarmSalePaymentListener
{
    /**
     * Handle a PaymentSucceeded event.
     * Updates FarmSale.payment_status based on total successful transactions.
     */
    public function handle(PaymentSucceeded $event): void
    {
        $intent = $event->payIntent;

        // Only handle intents whose payable is a FarmSale
        if ($intent->payable_type !== FarmSale::class) {
            return;
        }

        $sale = FarmSale::find($intent->payable_id);
        if (! $sale) {
            return;
        }

        // Sum all successful transaction amounts across ALL intents for this sale
        $totalPaid = \Modules\PaymentsChannel\Models\PayTransaction::query()
            ->whereIn('pay_intent_id', $sale->payIntents()->pluck('id'))
            ->where('status', 'successful')
            ->where('transaction_type', 'payment')
            ->sum('amount');

        $newStatus = match (true) {
            $totalPaid <= 0                              => 'pending',
            $totalPaid >= (float) $sale->total_amount   => 'paid',
            default                                      => 'partial',
        };

        if ($sale->payment_status !== $newStatus) {
            $sale->update(['payment_status' => $newStatus]);
        }
    }
}