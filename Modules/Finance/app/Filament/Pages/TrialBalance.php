<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Account;

class TrialBalance extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Trial Balance';

    protected string $view = 'finance::filament.pages.trial-balance';

    public ?string $asOf = null;

    public array $rows = [];

    public float $totalDebits  = 0;
    public float $totalCredits = 0;
    public bool  $balanced     = true;

    public function mount(): void
    {
        $this->asOf = now()->toDateString();
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;
        $asOf      = $this->asOf ?? now()->toDateString();

        $accounts = Account::query()
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->withSum(['journalLines as total_debit'  => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'debit')
            ->withSum(['journalLines as total_credit' => fn ($q) => $q->whereHas('journalEntry', fn ($je) => $je->whereDate('date', '<=', $asOf))], 'credit')
            ->orderBy('code')
            ->get()
            ->filter(fn ($a) => ($a->total_debit > 0 || $a->total_credit > 0));

        $this->rows = $accounts->map(function ($account) {
            $debit  = (float) ($account->total_debit  ?? 0);
            $credit = (float) ($account->total_credit ?? 0);
            $net    = $debit - $credit;

            return [
                'code'    => $account->code,
                'name'    => $account->name,
                'type'    => ucfirst($account->type),
                'debit'   => $debit > $credit ? $net : 0,
                'credit'  => $credit > $debit ? abs($net) : 0,
            ];
        })->values()->toArray();

        $this->totalDebits  = collect($this->rows)->sum('debit');
        $this->totalCredits = collect($this->rows)->sum('credit');
        $this->balanced     = abs($this->totalDebits - $this->totalCredits) < 0.01;
    }
}
