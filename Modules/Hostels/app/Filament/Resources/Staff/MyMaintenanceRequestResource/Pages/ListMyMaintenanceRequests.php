<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource;

class ListMyMaintenanceRequests extends ListRecords
{
    protected static string $resource = MyMaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
