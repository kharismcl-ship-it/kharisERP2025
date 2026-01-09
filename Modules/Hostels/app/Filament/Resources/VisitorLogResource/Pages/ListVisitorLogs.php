<?php

namespace Modules\Hostels\Filament\Resources\VisitorLogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\VisitorLogResource;

class ListVisitorLogs extends ListRecords
{
    protected static string $resource = VisitorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
