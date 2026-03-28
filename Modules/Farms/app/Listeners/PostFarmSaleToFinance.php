<?php

namespace Modules\Farms\Listeners;

use Modules\Farms\Events\FarmSaleCreated;

class PostFarmSaleToFinance
{
    public function handle(FarmSaleCreated $event): void
    {
        $sale = $event->sale ?? null;
        if (! $sale) {
            return;
        }

        // Only post if Finance module is available
        if (! class_exists(\Modules\Finance\Models\JournalEntry::class)) {
            return;
        }

        try {
            // Find a default "Farm Revenue" account (type: revenue)
            $revenueAccount = \Modules\Finance\Models\ChartOfAccount::where('company_id', $sale->company_id)
                ->where('account_type', 'revenue')
                ->where('account_name', 'like', '%Farm%')
                ->first();

            $arAccount = \Modules\Finance\Models\ChartOfAccount::where('company_id', $sale->company_id)
                ->where('account_type', 'asset')
                ->where('account_name', 'like', '%Receivable%')
                ->first();

            if (! $revenueAccount || ! $arAccount) {
                return;
            }

            $period = \Modules\Finance\Models\AccountingPeriod::where('company_id', $sale->company_id)
                ->where('status', 'open')
                ->first();

            if (! $period) {
                return;
            }

            $je = \Modules\Finance\Models\JournalEntry::create([
                'company_id'   => $sale->company_id,
                'period_id'    => $period->id,
                'entry_date'   => $sale->sale_date ?? now()->toDateString(),
                'reference'    => 'FARM-SALE-' . $sale->id,
                'description'  => 'Farm sale: ' . ($sale->buyer_name ?? 'Customer'),
                'total_debit'  => $sale->total_amount ?? 0,
                'total_credit' => $sale->total_amount ?? 0,
                'status'       => 'posted',
                'created_by'   => null,
            ]);

            // DR Accounts Receivable
            $je->lines()->create([
                'account_id'  => $arAccount->id,
                'description' => 'Farm sale receivable',
                'debit'       => $sale->total_amount ?? 0,
                'credit'      => 0,
            ]);

            // CR Farm Revenue
            $je->lines()->create([
                'account_id'  => $revenueAccount->id,
                'description' => 'Farm sale revenue',
                'debit'       => 0,
                'credit'      => $sale->total_amount ?? 0,
            ]);

            $sale->updateQuietly(['fin_journal_entry_id' => $je->id]);
        } catch (\Throwable $e) {
            // Silently fail — Finance integration is optional
            \Log::warning('Farm→Finance journal posting failed: ' . $e->getMessage());
        }
    }
}