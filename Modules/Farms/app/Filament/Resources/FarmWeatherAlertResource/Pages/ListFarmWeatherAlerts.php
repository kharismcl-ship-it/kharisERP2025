<?php

namespace Modules\Farms\Filament\Resources\FarmWeatherAlertResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmWeatherAlertResource;

class ListFarmWeatherAlerts extends ListRecords
{
    protected static string $resource = FarmWeatherAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}