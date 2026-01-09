<?php

namespace Modules\Hostels\Filament\Resources\MaintenanceRequestResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\MaintenanceRequestResource;

class CreateMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
