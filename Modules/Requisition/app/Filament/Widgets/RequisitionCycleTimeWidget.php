<?php

declare(strict_types=1);

namespace Modules\Requisition\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionApprover;

class RequisitionCycleTimeWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $month = now()->month;
        $year  = now()->year;

        // 1. Avg Time to First Approval (hours): created_at → first decided_at this month
        $avgHoursToApproval = RequisitionApprover::query()
            ->join('requisitions', 'requisitions.id', '=', 'requisition_approvers.requisition_id')
            ->whereNotNull('requisition_approvers.decided_at')
            ->whereIn('requisition_approvers.decision', ['approved', 'rejected'])
            ->whereYear('requisition_approvers.decided_at', $year)
            ->whereMonth('requisition_approvers.decided_at', $month)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, requisitions.created_at, requisition_approvers.decided_at)) as avg_hours')
            ->value('avg_hours');

        // 2. Avg Time to Fulfillment (days): created_at → fulfilled_at for fulfilled this month
        $avgDaysToFulfillment = Requisition::withoutGlobalScopes()
            ->where('status', 'fulfilled')
            ->whereNotNull('fulfilled_at')
            ->whereYear('fulfilled_at', $year)
            ->whereMonth('fulfilled_at', $month)
            ->selectRaw('AVG(DATEDIFF(fulfilled_at, created_at)) as avg_days')
            ->value('avg_days');

        // 3. Pending Approval Longest (days): oldest unresolved submitted/under_review
        $oldestPending = Requisition::withoutGlobalScopes()
            ->whereIn('status', ['submitted', 'under_review'])
            ->orderBy('created_at')
            ->first();

        $longestPendingDays = $oldestPending
            ? now()->diffInDays($oldestPending->created_at)
            : 0;

        // 4. Rejected Rate this month
        $approvedCount = Requisition::withoutGlobalScopes()
            ->where('status', 'approved')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $rejectedCount = Requisition::withoutGlobalScopes()
            ->where('status', 'rejected')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $total        = $approvedCount + $rejectedCount;
        $rejectedRate = $total > 0 ? round(($rejectedCount / $total) * 100, 1) : 0;

        return [
            Stat::make('Avg. Time to First Approval', $avgHoursToApproval ? round($avgHoursToApproval, 1) . ' hrs' : '—')
                ->description('Hours from creation to first approver decision (' . now()->format('M Y') . ')')
                ->icon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Avg. Time to Fulfilment', $avgDaysToFulfillment ? round($avgDaysToFulfillment, 1) . ' days' : '—')
                ->description('Days from creation to fulfilment (' . now()->format('M Y') . ')')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Longest Pending', $longestPendingDays > 0 ? $longestPendingDays . ' days' : 'None pending')
                ->description('Age of oldest unresolved submitted/under-review request')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($longestPendingDays > 7 ? 'danger' : ($longestPendingDays > 3 ? 'warning' : 'success')),

            Stat::make('Rejection Rate', $rejectedRate . '%')
                ->description('Rejected / (Approved + Rejected) in ' . now()->format('M Y'))
                ->icon('heroicon-o-x-circle')
                ->color($rejectedRate > 30 ? 'danger' : ($rejectedRate > 15 ? 'warning' : 'success')),
        ];
    }
}