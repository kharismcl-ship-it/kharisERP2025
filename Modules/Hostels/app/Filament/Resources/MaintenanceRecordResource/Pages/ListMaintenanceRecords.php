<?php

namespace Modules\Hostels\Filament\Resources\MaintenanceRecordResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\MaintenanceRecordResource;

class ListMaintenanceRecords extends ListRecords
{
    protected static string $resource = MaintenanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Maintenance Record'),
        ];
    }
}
