<?php

namespace Modules\Farms\Listeners;

use Modules\Farms\Events\FarmExpenseRecorded;

class PostFarmExpenseToFinance
{
    public function handle(FarmExpenseRecorded $event): void
    {
        $expense = $event->expense ?? null;
        if (! $expense) {
            return;
        }

        // Only post if Finance module is available
        if (! class_exists(\Modules\Finance\Models\JournalEntry::class)) {
            return;
        }

        try {
            // Find a default "Farm Expense" account (type: expense)
            $expenseAccount = \Modules\Finance\Models\ChartOfAccount::where('company_id', $expense->company_id)
                ->where('account_type', 'expense')
                ->where('account_name', 'like', '%Farm%')
                ->first();

            // Fallback: Accounts Payable / Cash
            $apAccount = \Modules\Finance\Models\ChartOfAccount::where('company_id', $expense->company_id)
                ->where('account_type', 'liability')
                ->where('account_name', 'like', '%Payable%')
                ->first();

            if (! $expenseAccount || ! $apAccount) {
                return;
            }

            $period = \Modules\Finance\Models\AccountingPeriod::where('company_id', $expense->company_id)
                ->where('status', 'open')
                ->first();

            if (! $period) {
                return;
            }

            $amount = $expense->amount ?? 0;

            $je = \Modules\Finance\Models\JournalEntry::create([
                'company_id'   => $expense->company_id,
                'period_id'    => $period->id,
                'entry_date'   => $expense->expense_date ?? now()->toDateString(),
                'reference'    => 'FARM-EXP-' . $expense->id,
                'description'  => 'Farm expense: ' . ($expense->description ?? $expense->category ?? 'Expense'),
                'total_debit'  => $amount,
                'total_credit' => $amount,
                'status'       => 'posted',
                'created_by'   => null,
            ]);

            // DR Farm Expense
            $je->lines()->create([
                'account_id'  => $expenseAccount->id,
                'description' => 'Farm expense',
                'debit'       => $amount,
                'credit'      => 0,
            ]);

            // CR Accounts Payable
            $je->lines()->create([
                'account_id'  => $apAccount->id,
                'description' => 'Farm expense payable',
                'debit'       => 0,
                'credit'      => $amount,
            ]);

            $expense->updateQuietly(['fin_journal_entry_id' => $je->id]);
        } catch (\Throwable $e) {
            // Silently fail — Finance integration is optional
            \Log::warning('Farm→Finance expense journal posting failed: ' . $e->getMessage());
        }
    }
}