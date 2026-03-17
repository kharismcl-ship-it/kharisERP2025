<?php

namespace Modules\Farms\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\CropScoutingRecord;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmBudget;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\LivestockHealthRecord;

class FarmDashboardStatsWidget extends StatsOverviewWidget
{
    // Only rendered when explicitly included via getHeaderWidgets() / getFooterWidgets()
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $cid  = auth()->user()?->current_company_id;
        $year = now()->year;

        $s = fn ($model) => $model::query()->when($cid, fn ($q) => $q->where('company_id', $cid));

        $ytdRevenue  = $s(HarvestRecord::class)->whereYear('harvest_date', $year)->sum('total_revenue')
                     + $s(FarmSale::class)->whereYear('sale_date', $year)->sum('total_amount');
        $ytdExpenses = $s(FarmExpense::class)->whereYear('expense_date', $year)->sum('amount');
        $netProfit   = $ytdRevenue - $ytdExpenses;

        $totalBudgeted    = $s(FarmBudget::class)->where('budget_year', $year)->sum('budgeted_amount');
        $budgetUtil       = $totalBudgeted > 0 ? round(($ytdExpenses / $totalBudgeted) * 100, 1) : null;

        $openTasks    = $s(FarmTask::class)->whereNull('completed_at')->count();
        $overdueTasks = $s(FarmTask::class)->whereNull('completed_at')
            ->whereNotNull('due_date')->whereDate('due_date', '<', now())->count();

        $activeBatches    = $s(LivestockBatch::class)->where('status', 'active');
        $livestockCount   = (clone $activeBatches)->sum('current_count');
        $livestockBatches = (clone $activeBatches)->count();

        $healthDue = $s(LivestockHealthRecord::class)
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', now()->addDays(7))
            ->count();

        return [
            Stat::make('Total Farms', $s(Farm::class)->count())
                ->icon('heroicon-o-map')
                ->color('primary'),

            Stat::make('Active Crop Cycles', $s(CropCycle::class)->where('status', 'growing')->count())
                ->icon('heroicon-o-sun')
                ->color('info'),

            Stat::make('Livestock (Active)', number_format($livestockCount))
                ->description($livestockBatches . ' active batch' . ($livestockBatches !== 1 ? 'es' : ''))
                ->icon('heroicon-o-cube')
                ->color('primary'),

            Stat::make('YTD Revenue', 'GHS ' . number_format($ytdRevenue, 2))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('YTD Expenses', 'GHS ' . number_format($ytdExpenses, 2))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Profit (YTD)', 'GHS ' . number_format($netProfit, 2))
                ->icon('heroicon-o-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

            Stat::make(
                'Budget Utilisation',
                $budgetUtil !== null ? $budgetUtil . '%' : 'No budget set'
            )
                ->description($totalBudgeted > 0 ? 'of GHS ' . number_format($totalBudgeted, 2) . ' budgeted' : null)
                ->icon('heroicon-o-chart-bar')
                ->color($budgetUtil !== null && $budgetUtil > 100 ? 'danger' : 'success'),

            Stat::make('Open Tasks', $openTasks)
                ->description($overdueTasks > 0 ? $overdueTasks . ' overdue' : null)
                ->icon('heroicon-o-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'gray'),

            Stat::make('Health Alerts (7 days)', $healthDue)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($healthDue > 0 ? 'warning' : 'success'),
        ];
    }
}
