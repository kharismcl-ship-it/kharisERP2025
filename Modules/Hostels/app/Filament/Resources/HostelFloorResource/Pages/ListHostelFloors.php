<?php

namespace Modules\Hostels\Filament\Resources\HostelFloorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelFloorResource;

class ListHostelFloors extends ListRecords
{
    protected static string $resource = HostelFloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
