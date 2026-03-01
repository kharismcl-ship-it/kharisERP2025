<?php

namespace Modules\Finance\Listeners\ManufacturingWater;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\ManufacturingWater\Events\MwDistributionCompleted;

class CreateInvoiceForWaterDistribution
{
    /**
     * When water is distributed (sold), create a Finance invoice and post GL:
     *
     *   DR 1110 Accounts Receivable   total_amount
     *   CR 4300 Water Revenue         total_amount
     */
    public function handle(MwDistributionCompleted $event): void
    {
        $record    = $event->distributionRecord;
        $companyId = $record->company_id;
        $amount    = (float) $record->total_amount;

        if ($amount <= 0) {
            return;
        }

        try {
            $invoice = Invoice::create([
                'company_id'  => $companyId,
                'type'        => 'customer',
                'module'      => 'manufacturing_water',
                'status'      => 'sent',
                'reference'   => $record->distribution_reference,
                'description' => "Water distribution — {$record->destination}",
                'issue_date'  => $record->distribution_date ?? now(),
                'due_date'    => ($record->distribution_date ?? now())->addDays(30),
                'total'       => $amount,
            ]);

            InvoiceLine::create([
                'invoice_id'  => $invoice->id,
                'description' => "Water distribution to {$record->destination} ({$record->volume_liters}L @ {$record->unit_price}/L)",
                'quantity'    => (float) $record->volume_liters,
                'unit_price'  => (float) $record->unit_price,
                'total'       => $amount,
            ]);

            // Post GL
            $entry = JournalEntry::create([
                'company_id'  => $companyId,
                'date'        => $record->distribution_date ?? now(),
                'reference'   => 'MW-DIST-' . $record->id,
                'description' => "Water distribution revenue — {$record->distribution_reference}",
            ]);

            $lines = [];

            // DR Accounts Receivable (1110)
            if ($account = $this->account('1110', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => $amount, 'credit' => 0];
            }

            // CR Water Revenue (4300)
            if ($account = $this->account('4300', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $amount];
            }

            foreach ($lines as $line) {
                JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
            }

            Log::info('CreateInvoiceForWaterDistribution: invoice and GL posted', [
                'invoice_id'             => $invoice->id,
                'journal_entry_id'       => $entry->id,
                'distribution_record_id' => $record->id,
                'amount'                 => $amount,
            ]);
        } catch (\Throwable $e) {
            Log::warning('CreateInvoiceForWaterDistribution failed', [
                'distribution_record_id' => $record->id,
                'error'                  => $e->getMessage(),
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