<?php

namespace Modules\Fleet\Filament\Resources\MaintenanceRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Fleet\Filament\Resources\MaintenanceRecordResource;

class ListMaintenanceRecords extends ListRecords
{
    protected static string $resource = MaintenanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}