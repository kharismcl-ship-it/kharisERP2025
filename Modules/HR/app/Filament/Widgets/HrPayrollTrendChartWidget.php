<?php

namespace Modules\HR\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Modules\HR\Models\PayrollRun;

class HrPayrollTrendChartWidget extends ChartWidget
{
    protected ?string $heading = 'Payroll Trend — Gross vs Net (Last 6 Paid Runs)';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
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
        $rows = PayrollRun::query()
            ->when($cid, fn ($q) => $q->where('company_id', $cid))
            ->where('status', 'paid')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(6)
            ->get(['period_year', 'period_month', 'total_gross', 'total_net', 'total_deductions'])
            ->sortBy(fn ($r) => $r->period_year * 100 + $r->period_month)
            ->values();

        return [
            'labels'   => $rows->map(fn ($r) => Carbon::create($r->period_year, $r->period_month)->format('M Y'))->toArray(),
            'datasets' => [
                [
                    'label'           => 'Gross',
                    'data'            => $rows->pluck('total_gross')->map(fn ($v) => round((float) $v, 2))->toArray(),
                    'borderColor'     => 'rgba(139, 92, 246, 0.9)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.35,
                    'pointRadius'     => 3,
                ],
                [
                    'label'           => 'Net',
                    'data'            => $rows->pluck('total_net')->map(fn ($v) => round((float) $v, 2))->toArray(),
                    'borderColor'     => 'rgba(34, 197, 94, 0.9)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.08)',
                    'fill'            => true,
                    'tension'         => 0.35,
                    'pointRadius'     => 3,
                ],
                [
                    'label'           => 'Deductions',
                    'data'            => $rows->pluck('total_deductions')->map(fn ($v) => round((float) $v, 2))->toArray(),
                    'borderColor'     => 'rgba(239, 68, 68, 0.8)',
                    'backgroundColor' => 'transparent',
                    'borderDash'      => [4, 3],
                    'tension'         => 0.35,
                    'pointRadius'     => 3,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'top'],
            ],
            'scales' => [
                'y' => ['beginAtZero' => false],
            ],
        ];
    }
}
