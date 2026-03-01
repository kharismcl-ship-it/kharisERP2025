<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalLine;

class IncomeStatement extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 51;

    protected static ?string $navigationLabel = 'Income Statement';

    protected string $view = 'finance::filament.pages.income-statement';

    public ?string $fromDate = null;
    public ?string $toDate   = null;

    public array $incomeRows  = [];
    public array $expenseRows = [];
    public float $totalIncome   = 0;
    public float $totalExpenses = 0;
    public float $netProfit     = 0;

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate   = now()->toDateString();
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;
        $from      = $this->fromDate ?? now()->startOfMonth()->toDateString();
        $to        = $this->toDate   ?? now()->toDateString();

        $this->incomeRows  = $this->getAccountRows('income',  $companyId, $from, $to);
        $this->expenseRows = $this->getAccountRows('expense', $companyId, $from, $to);

        $this->totalIncome   = collect($this->incomeRows)->sum('amount');
        $this->totalExpenses = collect($this->expenseRows)->sum('amount');
        $this->netProfit     = $this->totalIncome - $this->totalExpenses;
    }

    private function getAccountRows(string $type, ?int $companyId, string $from, string $to): array
    {
        $accounts = Account::where('type', $type)
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum([
                'journalLines as total_debit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to])),
            ], 'debit')
            ->withSum([
                'journalLines as total_credit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to])),
            ], 'credit')
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($type) {
                $debit  = (float) ($account->total_debit  ?? 0);
                $credit = (float) ($account->total_credit ?? 0);
                // Income: net is credit - debit; Expense: net is debit - credit
                $amount = $type === 'income' ? ($credit - $debit) : ($debit - $credit);

                return [
                    'code'   => $account->code,
                    'name'   => $account->name,
                    'amount' => max(0, $amount),
                ];
            })
            ->filter(fn ($r) => $r['amount'] > 0)
            ->values()
            ->toArray();

        return $accounts;
    }
}
