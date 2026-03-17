<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource;

class ListMyVisitorLogs extends ListRecords
{
    protected static string $resource = MyVisitorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
