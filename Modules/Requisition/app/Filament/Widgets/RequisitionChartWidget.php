<?php

namespace Modules\Requisition\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Requisition\Models\Requisition;

class RequisitionChartWidget extends ChartWidget
{
    protected ?string $heading = 'Requisition Trends (Last 6 Months)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months     = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));
        $labels     = $months->map(fn ($m) => $m->format('M Y'))->toArray();

        $submitted  = $months->map(fn ($m) => Requisition::withoutGlobalScopes()
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->count()
        )->toArray();

        $approved   = $months->map(fn ($m) => Requisition::withoutGlobalScopes()
            ->where('status', 'approved')
            ->whereYear('updated_at', $m->year)
            ->whereMonth('updated_at', $m->month)
            ->count()
        )->toArray();

        $fulfilled  = $months->map(fn ($m) => Requisition::withoutGlobalScopes()
            ->where('status', 'fulfilled')
            ->whereYear('updated_at', $m->year)
            ->whereMonth('updated_at', $m->month)
            ->count()
        )->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Submitted',
                    'data'            => $submitted,
                    'borderColor'     => '#6366f1',
                    'backgroundColor' => 'rgba(99,102,241,0.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Approved',
                    'data'            => $approved,
                    'borderColor'     => '#22c55e',
                    'backgroundColor' => 'rgba(34,197,94,0.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Fulfilled',
                    'data'            => $fulfilled,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}