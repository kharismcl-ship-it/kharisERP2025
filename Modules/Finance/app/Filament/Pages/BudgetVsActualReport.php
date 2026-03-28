<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\JournalLine;

class BudgetVsActualReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 57;

    protected static ?string $navigationLabel = 'Budget vs Actual';

    protected string $view = 'finance::filament.pages.budget-vs-actual';

    public ?int $budget_id = null;

    public ?string $as_of_date = null;

    public array $rows = [];

    public array $budgets = [];

    public function mount(): void
    {
        $this->as_of_date = now()->toDateString();
        $companyId = auth()->user()?->current_company_id;

        $this->budgets = Budget::when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function generate(): void
    {
        if (!$this->budget_id) {
            return;
        }

        $budget = Budget::with('lines.account')->find($this->budget_id);
        if (!$budget) {
            return;
        }

        $year = $budget->budget_year;
        $asOf = $this->as_of_date ?? now()->toDateString();

        $this->rows = $budget->lines->map(function ($line) use ($year, $asOf) {
            $accountId = $line->account_id;

            $actual = JournalLine::where('account_id', $accountId)
                ->whereHas('journalEntry', function ($q) use ($year, $asOf) {
                    $q->whereYear('date', $year)->whereDate('date', '<=', $asOf);
                })
                ->selectRaw('SUM(debit) - SUM(credit) as net')
                ->value('net') ?? 0;

            $budgetAmt = (float) $line->annual_total;
            $actualAmt = (float) $actual;
            $variance  = $actualAmt - $budgetAmt;
            $variancePct = $budgetAmt != 0 ? ($variance / $budgetAmt) * 100 : 0;

            return [
                'account'      => $line->account ? $line->account->name : 'Unknown',
                'budget'       => $budgetAmt,
                'actual'       => $actualAmt,
                'variance'     => $variance,
                'variance_pct' => $variancePct,
            ];
        })->values()->toArray();
    }
}