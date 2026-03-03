<?php

namespace Modules\HR\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;

class HrWorkforceStatsWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function companyId(): ?int
    {
        $tenant = Filament::getTenant();

        return $tenant?->getKey()
            ?? auth()->user()->current_company_id
            ?? null;
    }

    protected function scope(string $model): \Illuminate\Database\Eloquent\Builder
    {
        $q   = $model::query();
        $cid = $this->companyId();

        if ($cid) {
            $q->where('company_id', $cid);
        }

        return $q;
    }

    protected function getStats(): array
    {
        $today      = today();
        $monthStart = now()->startOfMonth();

        $empBase  = $this->scope(Employee::class);
        $total    = (clone $empBase)->count();
        $active   = (clone $empBase)->where('employment_status', 'active')->count();
        $inactive = $total - $active;
        $newHires = (clone $empBase)->where('hire_date', '>=', $monthStart)->count();

        $leaveBase         = $this->scope(LeaveRequest::class);
        $onLeaveToday      = (clone $leaveBase)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();
        $pendingLeave      = (clone $leaveBase)->where('status', 'pending')->count();
        $approvedThisMonth = (clone $leaveBase)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $monthStart)
            ->count();

        return [
            Stat::make('Total Employees', $total)
                ->description("{$inactive} inactive")
                ->descriptionIcon('heroicon-m-user-minus')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active Staff', $active)
                ->description($total > 0 ? round($active / $total * 100).'% of workforce' : 'No employees')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('On Leave Today', $onLeaveToday)
                ->description("{$pendingLeave} pending approval")
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-calendar-days')
                ->color($onLeaveToday > 0 ? 'warning' : 'success'),

            Stat::make('Pending Leave Requests', $pendingLeave)
                ->description("{$approvedThisMonth} approved this month")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-clock')
                ->color($pendingLeave > 0 ? 'warning' : 'success'),

            Stat::make('New Hires', $newHires)
                ->description('This month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->icon('heroicon-o-user-plus')
                ->color('info'),
        ];
    }
}
