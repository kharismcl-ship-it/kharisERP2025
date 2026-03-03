<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Account;

class CashFlowStatement extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 56;

    protected static ?string $navigationLabel = 'Cash Flow';

    protected string $view = 'finance::filament.pages.cash-flow-statement';

    public ?string $fromDate = null;
    public ?string $toDate   = null;

    // Operating
    public float $netIncome           = 0;
    public float $netCashFromOperating = 0;

    // Investing — net change in non-cash asset accounts
    public array $investingRows       = [];
    public float $netCashFromInvesting = 0;

    // Financing — net change in liability + equity accounts
    public array $financingRows       = [];
    public float $netCashFromFinancing = 0;

    // Net change
    public float $netCashChange = 0;

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

        // Operating: net income (income credits - debits; expense debits - credits)
        $this->netIncome = $this->calcNetIncome($companyId, $from, $to);
        $this->netCashFromOperating = $this->netIncome;

        // Investing: net change in asset accounts (non-cash: those not containing 'cash'/'bank' in name)
        $this->investingRows = $this->calcAccountTypeChange('asset', $companyId, $from, $to, excludeCashAccounts: true);
        // Asset debit net increase = cash used (negative investing CF); asset credit net decrease = cash received
        $this->netCashFromInvesting = collect($this->investingRows)->sum(fn ($r) => $r['cashImpact']);

        // Financing: net change in liability + equity accounts
        $liabilityRows = $this->calcAccountTypeChange('liability', $companyId, $from, $to);
        $equityRows    = $this->calcAccountTypeChange('equity',    $companyId, $from, $to);
        $this->financingRows = array_merge($liabilityRows, $equityRows);
        $this->netCashFromFinancing = collect($this->financingRows)->sum(fn ($r) => $r['cashImpact']);

        $this->netCashChange = round(
            $this->netCashFromOperating + $this->netCashFromInvesting + $this->netCashFromFinancing,
            2
        );
    }

    private function calcNetIncome(?int $companyId, string $from, string $to): float
    {
        $income = Account::where('type', 'income')
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as td' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to]))], 'debit')
            ->withSum(['journalLines as tc' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to]))], 'credit')
            ->get()->sum(fn ($a) => (float)($a->tc ?? 0) - (float)($a->td ?? 0));

        $expense = Account::where('type', 'expense')
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as td' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to]))], 'debit')
            ->withSum(['journalLines as tc' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to]))], 'credit')
            ->get()->sum(fn ($a) => (float)($a->td ?? 0) - (float)($a->tc ?? 0));

        return round($income - $expense, 2);
    }

    /**
     * For assets: debit net = increase in asset = cash used (negative cash impact)
     * For liabilities/equity: credit net = increase = cash received (positive cash impact)
     */
    private function calcAccountTypeChange(
        string $type,
        ?int   $companyId,
        string $from,
        string $to,
        bool   $excludeCashAccounts = false
    ): array {
        return Account::where('type', $type)
            ->when($excludeCashAccounts, fn ($q) => $q->where(fn ($q2) => $q2
                ->whereRaw("LOWER(name) NOT LIKE '%cash%'")
                ->whereRaw("LOWER(name) NOT LIKE '%bank%'")
            ))
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as total_debit'  => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to]))], 'debit')
            ->withSum(['journalLines as total_credit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereBetween('date', [$from, $to]))], 'credit')
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($type) {
                $debit  = (float) ($account->total_debit  ?? 0);
                $credit = (float) ($account->total_credit ?? 0);

                if ($debit == 0 && $credit == 0) return null;

                // Asset increase (debit net) = cash outflow (negative)
                // Liability/equity increase (credit net) = cash inflow (positive)
                $cashImpact = $type === 'asset'
                    ? round($credit - $debit, 2)   // asset: if credit > debit, asset decreased → cash in
                    : round($credit - $debit, 2);  // liability/equity: if credit > debit, liability grew → cash in

                return [
                    'code'       => $account->code,
                    'name'       => $account->name,
                    'debit'      => $debit,
                    'credit'     => $credit,
                    'cashImpact' => $cashImpact,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
