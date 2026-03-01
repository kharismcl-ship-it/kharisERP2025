<?php

namespace Modules\Finance\Listeners\Farms;

use Illuminate\Support\Facades\Log;
use Modules\Farms\Events\FarmSaleCreated;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;

class CreateInvoiceForFarmSale
{
    /**
     * When a farm sale is created, issue a Finance invoice and post GL:
     *
     *   DR 1110 Accounts Receivable   total_amount
     *   CR 4400 Farm / Agricultural Revenue   total_amount
     */
    public function handle(FarmSaleCreated $event): void
    {
        $sale      = $event->farmSale;
        $companyId = $sale->company_id;
        $amount    = (float) $sale->total_amount;

        if ($amount <= 0) {
            return;
        }

        // Skip if invoice already exists
        if ($sale->invoice_id) {
            return;
        }

        try {
            $invoice = Invoice::create([
                'company_id'  => $companyId,
                'type'        => 'customer',
                'module'      => 'farms',
                'status'      => 'sent',
                'reference'   => 'FARM-SALE-' . $sale->id,
                'description' => "Farm sale — {$sale->product_name} ({$sale->farm?->name})",
                'issue_date'  => $sale->sale_date ?? now(),
                'due_date'    => ($sale->sale_date ?? now())->addDays(14),
                'total'       => $amount,
            ]);

            InvoiceLine::create([
                'invoice_id'  => $invoice->id,
                'description' => "{$sale->product_name} ({$sale->quantity} {$sale->unit} @ {$sale->unit_price}/unit)",
                'quantity'    => (float) $sale->quantity,
                'unit_price'  => (float) $sale->unit_price,
                'total'       => $amount,
            ]);

            // Link invoice back to sale
            $sale->updateQuietly(['invoice_id' => $invoice->id]);

            // Post GL double-entry
            $entry = JournalEntry::create([
                'company_id'  => $companyId,
                'date'        => $sale->sale_date ?? now(),
                'reference'   => 'FARM-SALE-' . $sale->id,
                'description' => "Farm sale revenue — {$sale->product_name} to {$sale->buyer_name}",
            ]);

            $lines = [];

            // DR Accounts Receivable (1110)
            if ($account = $this->account('1110', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => $amount, 'credit' => 0];
            }

            // CR Agricultural Revenue (4400)
            if ($account = $this->account('4400', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $amount];
            }

            foreach ($lines as $line) {
                JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
            }

            Log::info('CreateInvoiceForFarmSale: invoice and GL posted', [
                'invoice_id'       => $invoice->id,
                'journal_entry_id' => $entry->id,
                'farm_sale_id'     => $sale->id,
                'amount'           => $amount,
            ]);
        } catch (\Throwable $e) {
            Log::warning('CreateInvoiceForFarmSale failed', [
                'farm_sale_id' => $sale->id,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    private function account(string $code, ?int $companyId): ?Account
    {
        return Account::where('code', $code)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->first();
    }
}
