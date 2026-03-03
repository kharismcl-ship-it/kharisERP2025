<?php

namespace Modules\Farms\Filament\Resources\FarmDailyReportResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmDailyReportResource;

class ViewFarmDailyReport extends ViewRecord
{
    protected static string $resource = FarmDailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
