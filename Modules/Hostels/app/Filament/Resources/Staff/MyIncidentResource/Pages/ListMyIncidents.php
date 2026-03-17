<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource;

class ListMyIncidents extends ListRecords
{
    protected static string $resource = MyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
