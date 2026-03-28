<?php

namespace Modules\Finance\Filament\Pages;

use App\Models\Company;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalLine;

class ConsolidatedReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 61;

    protected static ?string $navigationLabel = 'Group P&L';

    protected string $view = 'finance::filament.pages.consolidated-report';

    public ?string $date_from = null;

    public ?string $date_to = null;

    public array $companies = [];

    public function mount(): void
    {
        $this->date_from = now()->startOfYear()->toDateString();
        $this->date_to   = now()->toDateString();
    }

    public function generate(): void
    {
        $allCompanies = Company::all();

        $this->companies = $allCompanies->map(function (Company $company) {
            $incomeAccountIds = Account::where('company_id', $company->id)
                ->whereIn('type', ['income', 'revenue'])
                ->pluck('id');

            $expenseAccountIds = Account::where('company_id', $company->id)
                ->whereIn('type', ['expense'])
                ->pluck('id');

            $income = JournalLine::whereIn('account_id', $incomeAccountIds)
                ->whereHas('journalEntry', function ($q) use ($company) {
                    $q->when($this->date_from, fn ($q2) => $q2->whereDate('date', '>=', $this->date_from))
                      ->when($this->date_to, fn ($q2) => $q2->whereDate('date', '<=', $this->date_to));
                })
                ->selectRaw('SUM(credit) - SUM(debit) as net')
                ->value('net') ?? 0;

            $expenses = JournalLine::whereIn('account_id', $expenseAccountIds)
                ->whereHas('journalEntry', function ($q) use ($company) {
                    $q->when($this->date_from, fn ($q2) => $q2->whereDate('date', '>=', $this->date_from))
                      ->when($this->date_to, fn ($q2) => $q2->whereDate('date', '<=', $this->date_to));
                })
                ->selectRaw('SUM(debit) - SUM(credit) as net')
                ->value('net') ?? 0;

            return [
                'name'     => $company->name,
                'income'   => (float) $income,
                'expenses' => (float) $expenses,
                'net'      => (float) $income - (float) $expenses,
            ];
        })->values()->toArray();
    }
}