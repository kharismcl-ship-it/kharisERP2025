<?php

namespace Modules\Sales\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Sales\Events\PosSaleCompleted;

class CreateInvoiceForPosSale
{
    public function handle(PosSaleCompleted $event): void
    {
        $sale = $event->sale;

        if (! class_exists(\Modules\Finance\Models\Invoice::class)) {
            return;
        }

        try {
            $invoice = \Modules\Finance\Models\Invoice::create([
                'company_id'   => optional($sale->session->terminal)->company_id ?? null,
                'invoice_date' => now()->toDateString(),
                'due_date'     => now()->toDateString(), // POS = immediate payment
                'total'        => $sale->total,
                'status'       => 'active',
                'notes'        => 'POS Sale ' . $sale->reference,
            ]);

            $sale->update(['invoice_id' => $invoice->id]);
        } catch (\Throwable $e) {
            Log::error("CreateInvoiceForPosSale failed for sale {$sale->id}: {$e->getMessage()}");
        }
    }
}