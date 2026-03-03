<?php

namespace Modules\HR\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Employee;

class HrHeadcountChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Active Headcount by Department';

    protected int | string | array $columnSpan = 2;

    protected function getType(): string
    {
        return 'bar';
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
        $cid = $this->companyId();

        $rows = Employee::query()
            ->when($cid, fn ($q) => $q->where('hr_employees.company_id', $cid))
            ->where('hr_employees.employment_status', 'active')
            ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->select('hr_departments.name', DB::raw('count(*) as cnt'))
            ->groupBy('hr_departments.id', 'hr_departments.name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        return [
            'labels'   => $rows->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label'           => 'Employees',
                    'data'            => $rows->pluck('cnt')->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.75)',
                    'borderColor'     => 'rgb(99, 102, 241)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                ],
            ],
        ];
    }
}
