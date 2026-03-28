<?php

namespace Modules\Requisition\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Finance\Models\CostCentre;
use Modules\Requisition\Models\Requisition;

class RequisitionStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pending   = Requisition::withoutGlobalScopes()->whereIn('status', ['draft', 'submitted', 'under_review', 'pending_revision'])->count();
        $approved  = Requisition::withoutGlobalScopes()->where('status', 'approved')->whereMonth('created_at', now()->month)->count();
        $rejected  = Requisition::withoutGlobalScopes()->where('status', 'rejected')->whereMonth('created_at', now()->month)->count();
        $fulfilled = Requisition::withoutGlobalScopes()->where('status', 'fulfilled')->whereMonth('created_at', now()->month)->count();
        $overdue   = Requisition::withoutGlobalScopes()
            ->whereNotNull('due_by')
            ->where('due_by', '<', now()->toDateString())
            ->whereNotIn('status', ['approved', 'fulfilled', 'rejected'])
            ->count();

        // Average resolution time: submitted → fulfilled (in days)
        $avgDays = Requisition::withoutGlobalScopes()
            ->where('status', 'fulfilled')
            ->whereNotNull('fulfilled_at')
            ->selectRaw('AVG(DATEDIFF(fulfilled_at, created_at)) as avg_days')
            ->value('avg_days');

        // Total spend this month (approved + fulfilled)
        $totalSpendThisMonth = Requisition::withoutGlobalScopes()
            ->whereIn('status', ['approved', 'fulfilled'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_estimated_cost');

        // Budget utilisation: sum of used / sum of budgets across all active cost centres
        $activeCostCentreIds = CostCentre::whereNotNull('budget_amount')
            ->where('budget_amount', '>', 0)
            ->pluck('id')
            ->toArray();

        $totalBudget = CostCentre::whereIn('id', $activeCostCentreIds)->sum('budget_amount');

        $totalCommitted = $activeCostCentreIds
            ? Requisition::withoutGlobalScopes()
                ->whereIn('cost_centre_id', $activeCostCentreIds)
                ->whereNotIn('status', ['rejected', 'closed', 'cancelled'])
                ->sum('total_estimated_cost')
            : 0;

        $budgetUtil = ($totalBudget > 0)
            ? round(((float) $totalCommitted / (float) $totalBudget) * 100, 1)
            : 0;

        return [
            Stat::make('Pending Requests', $pending)
                ->description('Draft, submitted, under review, awaiting revision')
                ->icon('heroicon-o-clock')
                ->color($pending > 0 ? 'warning' : 'success'),

            Stat::make('Approved This Month', $approved)
                ->description('Requisitions approved in ' . now()->format('F Y'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Fulfilled This Month', $fulfilled)
                ->description('Completed requisitions in ' . now()->format('F Y'))
                ->icon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Rejected This Month', $rejected)
                ->description('Requisitions rejected in ' . now()->format('F Y'))
                ->icon('heroicon-o-x-circle')
                ->color($rejected > 0 ? 'danger' : 'gray'),

            Stat::make('Overdue', $overdue)
                ->description('Past due date and not resolved')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'success'),

            Stat::make('Avg. Resolution Time', $avgDays ? round($avgDays, 1) . ' days' : '—')
                ->description('Average days from creation to fulfilment')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('Total Spend This Month', 'GHS ' . number_format((float) $totalSpendThisMonth, 2))
                ->description('Sum of approved + fulfilled requisitions in ' . now()->format('F Y'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Budget Utilisation', $budgetUtil . '%')
                ->description('Committed spend vs total cost centre budgets')
                ->icon('heroicon-o-chart-pie')
                ->color($budgetUtil > 90 ? 'danger' : ($budgetUtil > 70 ? 'warning' : 'success')),
        ];
    }
}
