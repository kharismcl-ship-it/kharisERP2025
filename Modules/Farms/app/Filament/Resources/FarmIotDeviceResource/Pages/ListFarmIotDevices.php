<?php

namespace Modules\Farms\Filament\Resources\FarmIotDeviceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmIotDeviceResource;

class ListFarmIotDevices extends ListRecords
{
    protected static string $resource = FarmIotDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}