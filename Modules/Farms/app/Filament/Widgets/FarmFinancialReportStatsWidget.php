<?php

namespace Modules\Farms\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;
use Modules\Farms\Models\FarmBudget;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\HarvestRecord;

class FarmFinancialReportStatsWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public int $year;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    #[On('financial-report-year-changed')]
    public function updateYear(int $year): void
    {
        $this->year = $year;
    }

    protected function getStats(): array
    {
        $cid   = auth()->user()?->current_company_id;
        $year  = $this->year;
        $scope = fn ($q) => $q->when($cid, fn ($q) => $q->where('company_id', $cid));

        $totalRevenue = HarvestRecord::query()->tap($scope)->whereYear('harvest_date', $year)->sum('total_revenue')
                      + FarmSale::query()->tap($scope)->whereYear('sale_date', $year)->sum('total_amount');

        $totalExpenses = FarmExpense::query()->tap($scope)->whereYear('expense_date', $year)->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;

        $totalBudgeted = FarmBudget::query()->tap($scope)->where('budget_year', $year)->sum('budgeted_amount');
        $budgetUtil    = $totalBudgeted > 0 ? round(($totalExpenses / $totalBudgeted) * 100, 1) : null;

        return [
            Stat::make('Total Revenue — ' . $year, 'GHS ' . number_format($totalRevenue, 2))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Expenses — ' . $year, 'GHS ' . number_format($totalExpenses, 2))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Profit — ' . $year, 'GHS ' . number_format($netProfit, 2))
                ->icon('heroicon-o-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Budget Utilisation', $budgetUtil !== null ? $budgetUtil . '%' : 'No budget set')
                ->description($totalBudgeted > 0 ? 'of GHS ' . number_format($totalBudgeted, 2) . ' budgeted' : null)
                ->icon('heroicon-o-chart-bar')
                ->color($budgetUtil !== null && $budgetUtil > 100 ? 'danger' : 'success'),
        ];
    }
}
