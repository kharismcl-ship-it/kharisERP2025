<?php

declare(strict_types=1);

namespace Modules\Requisition\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Requisition\Models\Requisition;

class RequisitionSpendWidget extends ChartWidget
{
    protected ?string $heading = 'Spend by Request Type (This Month)';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $month = now()->month;
        $year  = now()->year;

        $spendByType = Requisition::withoutGlobalScopes()
            ->whereIn('status', ['approved', 'fulfilled'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('total_estimated_cost')
            ->selectRaw('request_type, SUM(total_estimated_cost) as total_spend')
            ->groupBy('request_type')
            ->pluck('total_spend', 'request_type');

        $types  = array_keys(Requisition::TYPES);
        $labels = array_values(Requisition::TYPES);
        $data   = array_map(fn ($type) => (float) ($spendByType[$type] ?? 0), $types);

        $colors = [
            'rgba(99,102,241,0.7)',
            'rgba(245,158,11,0.7)',
            'rgba(59,130,246,0.7)',
            'rgba(34,197,94,0.7)',
            'rgba(168,85,247,0.7)',
            'rgba(107,114,128,0.7)',
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'GHS Spend',
                    'data'            => $data,
                    'backgroundColor' => array_slice($colors, 0, count($types)),
                    'borderColor'     => array_map(fn ($c) => str_replace('0.7', '1', $c), array_slice($colors, 0, count($types))),
                    'borderWidth'     => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}