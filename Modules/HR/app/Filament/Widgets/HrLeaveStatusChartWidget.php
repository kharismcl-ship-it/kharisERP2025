<?php

namespace Modules\HR\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\LeaveRequest;

class HrLeaveStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Leave Requests (Last 30 Days)';

    protected int | string | array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function companyId(): ?int
    {
        $tenant = Filament::getTenant();

        return $tenant?->getKey()
            ?? auth()->user()->current_company_id
            ?? null;
    }

    protected function getData(): array
    {
        $cid  = $this->companyId();
        $rows = LeaveRequest::query()
            ->when($cid, fn ($q) => $q->where('company_id', $cid))
            ->where('created_at', '>=', now()->subDays(30))
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->get();

        $colors = [
            'pending'   => 'rgba(245, 158, 11, 0.8)',
            'approved'  => 'rgba(34, 197, 94, 0.8)',
            'rejected'  => 'rgba(239, 68, 68, 0.8)',
            'cancelled' => 'rgba(156, 163, 175, 0.8)',
        ];

        return [
            'labels'   => $rows->pluck('status')->map(fn ($s) => ucfirst($s))->toArray(),
            'datasets' => [
                [
                    'data'            => $rows->pluck('cnt')->toArray(),
                    'backgroundColor' => $rows->pluck('status')->map(fn ($s) => $colors[$s] ?? 'rgba(156,163,175,0.8)')->toArray(),
                    'borderWidth'     => 2,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'right'],
            ],
            'cutout' => '60%',
        ];
    }
}
