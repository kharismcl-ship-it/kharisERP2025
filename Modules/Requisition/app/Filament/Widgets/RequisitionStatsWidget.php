<?php

namespace Modules\Requisition\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
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
        ];
    }
}
