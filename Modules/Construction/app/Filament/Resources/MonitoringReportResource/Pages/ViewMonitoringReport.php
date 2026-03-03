<?php

namespace Modules\Construction\Filament\Resources\MonitoringReportResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\MonitoringReportResource;

class ViewMonitoringReport extends ViewRecord
{
    protected static string $resource = MonitoringReportResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
