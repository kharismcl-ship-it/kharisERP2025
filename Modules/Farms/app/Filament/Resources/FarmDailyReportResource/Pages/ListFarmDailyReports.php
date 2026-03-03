<?php

namespace Modules\Farms\Filament\Resources\FarmDailyReportResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmDailyReportResource;

class ListFarmDailyReports extends ListRecords
{
    protected static string $resource = FarmDailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
