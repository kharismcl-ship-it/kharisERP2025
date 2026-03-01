<?php

namespace Modules\Finance\Listeners\Farms;

use Illuminate\Support\Facades\Log;
use Modules\Farms\Events\FarmExpenseRecorded;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;

class RecordFarmExpense
{
    /**
     * When a farm expense is recorded, post GL:
     *
     *   DR 6200 Farm / Agricultural Expense   amount
     *   CR 1120 Bank Account                  amount
     */
    public function handle(FarmExpenseRecorded $event): void
    {
        $expense   = $event->farmExpense;
        $companyId = $expense->company_id;
        $amount    = (float) $expense->amount;

        if ($amount <= 0) {
            return;
        }

        try {
            $entry = JournalEntry::create([
                'company_id'  => $companyId,
                'date'        => $expense->expense_date ?? now(),
                'reference'   => 'FARM-EXP-' . $expense->id,
                'description' => "Farm expense — {$expense->category}: {$expense->description}",
            ]);

            $lines = [];

            // DR Farm Expense (6200)
            if ($account = $this->account('6200', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => $amount, 'credit' => 0];
            }

            // CR Bank Account (1120)
            if ($account = $this->account('1120', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $amount];
            }

            foreach ($lines as $line) {
                JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
            }

            Log::info('RecordFarmExpense: GL posted', [
                'journal_entry_id' => $entry->id,
                'farm_expense_id'  => $expense->id,
                'amount'           => $amount,
                'category'         => $expense->category,
            ]);
        } catch (\Throwable $e) {
            Log::warning('RecordFarmExpense failed', [
                'farm_expense_id' => $expense->id,
                'error'           => $e->getMessage(),
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
