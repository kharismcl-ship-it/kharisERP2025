<?php

namespace Modules\Farms\Filament\Resources\FarmDailyReportResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmDailyReportResource;

class EditFarmDailyReport extends EditRecord
{
    protected static string $resource = FarmDailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
