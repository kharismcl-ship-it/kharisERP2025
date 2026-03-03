<?php

namespace Modules\Construction\Filament\Resources\MonitoringReportResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\MonitoringReportResource;

class ListMonitoringReports extends ListRecords
{
    protected static string $resource = MonitoringReportResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
