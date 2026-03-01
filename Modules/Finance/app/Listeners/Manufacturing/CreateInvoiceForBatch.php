<?php

namespace Modules\Finance\Listeners\Manufacturing;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\ManufacturingPaper\Events\MpBatchCompleted;

class CreateInvoiceForBatch
{
    /**
     * When a paper manufacturing batch is completed, post cost-of-production to GL.
     *
     * GL posting (production cost recognition):
     *   DR 1140 Finished Goods Inventory   production_cost
     *   CR 5010 Cost of Goods Sold         production_cost
     *
     * A sales invoice will be created separately when the batch is actually sold.
     */
    public function handle(MpBatchCompleted $event): void
    {
        $batch     = $event->batch;
        $companyId = $batch->company_id;
        $cost      = (float) $batch->production_cost;

        if ($cost <= 0) {
            return;
        }

        try {
            $entry = JournalEntry::create([
                'company_id'  => $companyId,
                'date'        => $batch->end_time ?? now(),
                'reference'   => 'MP-BATCH-' . $batch->batch_number,
                'description' => "Paper batch completed — {$batch->batch_number} ({$batch->quantity_produced} {$batch->unit})",
            ]);

            $lines = [];

            // DR Finished Goods Inventory (1140)
            if ($account = $this->account('1140', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => $cost, 'credit' => 0];
            }

            // CR COGS / Production Cost (5010)
            if ($account = $this->account('5010', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $cost];
            }

            foreach ($lines as $line) {
                JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
            }

            Log::info('CreateInvoiceForBatch: production cost GL posted', [
                'journal_entry_id' => $entry->id,
                'batch_id'         => $batch->id,
                'batch_number'     => $batch->batch_number,
                'production_cost'  => $cost,
            ]);
        } catch (\Throwable $e) {
            Log::warning('CreateInvoiceForBatch failed', [
                'batch_id' => $batch->id,
                'error'    => $e->getMessage(),
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
