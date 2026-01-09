<?php

namespace Modules\Hostels\Filament\Resources\BedResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\BedResource;

class ListBeds extends ListRecords
{
    protected static string $resource = BedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
