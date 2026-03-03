<?php

namespace Modules\Construction\Filament\Resources\MonitoringReportResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\MonitoringReportResource;

class EditMonitoringReport extends EditRecord
{
    protected static string $resource = MonitoringReportResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
