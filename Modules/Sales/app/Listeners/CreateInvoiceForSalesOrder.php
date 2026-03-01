<?php

namespace Modules\Sales\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Sales\Events\SalesOrderFulfilled;

class CreateInvoiceForSalesOrder
{
    public function handle(SalesOrderFulfilled $event): void
    {
        $order = $event->order;

        if (! class_exists(\Modules\Finance\Models\Invoice::class)) {
            return;
        }

        try {
            $invoice = \Modules\Finance\Models\Invoice::create([
                'company_id'   => $order->company_id,
                'invoice_date' => now()->toDateString(),
                'due_date'     => now()->addDays(30)->toDateString(),
                'total'        => $order->total,
                'status'       => 'active',
                'notes'        => 'Auto-created from Sales Order ' . $order->reference,
            ]);

            $order->update(['invoice_id' => $invoice->id]);
        } catch (\Throwable $e) {
            Log::error("CreateInvoiceForSalesOrder failed for order {$order->id}: {$e->getMessage()}");
        }
    }
}