<?php

namespace Modules\Requisition\Filament\Pages;

use Filament\Pages\Page;
use Modules\Requisition\Filament\Widgets\RequisitionChartWidget;
use Modules\Requisition\Filament\Widgets\RequisitionStatsWidget;

class RequisitionDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Requisition Dashboard';

    protected string $view = 'requisition::filament.pages.requisition-dashboard';

    protected function getWidgets(): array
    {
        return [
            RequisitionStatsWidget::class,
            RequisitionChartWidget::class,
        ];
    }
}