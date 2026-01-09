<?php

namespace Modules\Hostels\Filament\Resources\HostelOccupantResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelOccupantResource;

class ListHostelOccupants extends ListRecords
{
    protected static string $resource = HostelOccupantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
