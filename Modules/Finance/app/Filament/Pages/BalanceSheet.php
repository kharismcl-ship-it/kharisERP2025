<?php

namespace Modules\Finance\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Account;

class BalanceSheet extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 53;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Balance Sheet';

    protected string $view = 'finance::filament.pages.balance-sheet';

    public ?string $asOf = null;

    public array $assetRows      = [];
    public array $liabilityRows  = [];
    public array $equityRows     = [];

    public float $totalAssets      = 0;
    public float $totalLiabilities = 0;
    public float $totalEquity      = 0;
    public float $totalLiabEquity  = 0;
    public bool  $balanced         = true;

    public function mount(): void
    {
        $this->asOf = now()->toDateString();
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;
        $asOf      = $this->asOf ?? now()->toDateString();

        $this->assetRows     = $this->getRows('asset',     $companyId, $asOf);
        $this->liabilityRows = $this->getRows('liability', $companyId, $asOf);
        $this->equityRows    = $this->getRows('equity',    $companyId, $asOf);

        $this->totalAssets      = collect($this->assetRows)->sum('balance');
        $this->totalLiabilities = collect($this->liabilityRows)->sum('balance');
        $this->totalEquity      = collect($this->equityRows)->sum('balance');
        $this->totalLiabEquity  = $this->totalLiabilities + $this->totalEquity;

        // Retained earnings: net income from all periods up to asOf
        $retainedEarnings = $this->calcRetainedEarnings($companyId, $asOf);
        if ($retainedEarnings != 0) {
            $this->equityRows[] = [
                'code'    => '—',
                'name'    => 'Retained Earnings',
                'balance' => $retainedEarnings,
            ];
            $this->totalEquity     += $retainedEarnings;
            $this->totalLiabEquity += $retainedEarnings;
        }

        $this->balanced = abs($this->totalAssets - $this->totalLiabEquity) < 0.01;
    }

    private function getRows(string $type, ?int $companyId, string $asOf): array
    {
        return Account::where('type', $type)
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as total_debit'  => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'debit')
            ->withSum(['journalLines as total_credit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'credit')
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($type) {
                $debit  = (float) ($account->total_debit  ?? 0);
                $credit = (float) ($account->total_credit ?? 0);
                // Assets/Expenses: normal debit balance; Liabilities/Equity/Income: normal credit balance
                $balance = in_array($type, ['asset', 'expense'])
                    ? ($debit - $credit)
                    : ($credit - $debit);

                return [
                    'code'    => $account->code,
                    'name'    => $account->name,
                    'balance' => $balance,
                ];
            })
            ->filter(fn ($r) => abs($r['balance']) > 0.001)
            ->values()
            ->toArray();
    }

    private function calcRetainedEarnings(?int $companyId, string $asOf): float
    {
        $income = Account::where('type', 'income')
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as total_debit'  => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'debit')
            ->withSum(['journalLines as total_credit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'credit')
            ->get()
            ->sum(fn ($a) => (float)($a->total_credit ?? 0) - (float)($a->total_debit ?? 0));

        $expense = Account::where('type', 'expense')
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as total_debit'  => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'debit')
            ->withSum(['journalLines as total_credit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'credit')
            ->get()
            ->sum(fn ($a) => (float)($a->total_debit ?? 0) - (float)($a->total_credit ?? 0));

        return round($income - $expense, 2);
    }
}
