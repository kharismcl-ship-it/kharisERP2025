<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\RecurringInvoice;

class RecurringInvoiceService
{
    /** Generate invoices for all due recurring invoice templates */
    public function generateDue(): int
    {
        $generated = 0;

        RecurringInvoice::due()->chunk(50, function ($items) use (&$generated) {
            foreach ($items as $recurring) {
                try {
                    $this->generate($recurring);
                    $generated++;
                } catch (\Throwable $e) {
                    Log::error('RecurringInvoice generation failed', [
                        'recurring_invoice_id' => $recurring->id,
                        'error'                => $e->getMessage(),
                    ]);
                }
            }
        });

        return $generated;
    }

    /** Generate a single invoice from a recurring template */
    public function generate(RecurringInvoice $recurring): Invoice
    {
        return DB::transaction(function () use ($recurring) {
            $invoice = Invoice::create([
                'company_id'    => $recurring->company_id,
                'type'          => 'customer',
                'customer_name' => $recurring->customer_name,
                'customer_type' => $recurring->customer_type,
                'customer_id'   => $recurring->customer_id,
                'invoice_number' => $this->nextInvoiceNumber($recurring->company_id),
                'invoice_date'  => now()->toDateString(),
                'due_date'      => now()->addDays(30)->toDateString(),
                'status'        => 'sent',
                'sub_total'     => $recurring->amount,
                'tax_total'     => $recurring->tax_total,
                'total'         => (float) $recurring->amount + (float) $recurring->tax_total,
                'entity_type'   => 'recurring_invoice',
                'entity_id'     => $recurring->id,
                'module'        => 'Finance',
            ]);

            InvoiceLine::create([
                'invoice_id'  => $invoice->id,
                'description' => $recurring->description ?? 'Recurring charge',
                'quantity'    => 1,
                'unit_price'  => $recurring->amount,
                'line_total'  => $recurring->amount,
            ]);

            $recurring->advanceSchedule();

            return $invoice;
        });
    }

    private function nextInvoiceNumber(int $companyId): string
    {
        $count = Invoice::where('company_id', $companyId)->count() + 1;

        return 'INV-' . str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }
}
