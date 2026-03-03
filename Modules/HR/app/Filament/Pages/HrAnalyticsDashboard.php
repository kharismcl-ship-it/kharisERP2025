<?php

namespace Modules\HR\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\HR\Filament\Widgets\HrHeadcountChartWidget;
use Modules\HR\Filament\Widgets\HrLeaveStatusChartWidget;
use Modules\HR\Filament\Widgets\HrOperationsStatsWidget;
use Modules\HR\Filament\Widgets\HrPayrollTrendChartWidget;
use Modules\HR\Filament\Widgets\HrWorkforceStatsWidget;

class HrAnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'HR Dashboard';

    protected string $view = 'hr::filament.pages.hr-analytics-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            HrWorkforceStatsWidget::class,
            HrOperationsStatsWidget::class,
            HrHeadcountChartWidget::class,
            HrLeaveStatusChartWidget::class,
            HrPayrollTrendChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}
