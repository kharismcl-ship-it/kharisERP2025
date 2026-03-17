<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource;

class ListMyFarmDailyReports extends ListRecords
{
    protected static string $resource = MyFarmDailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
