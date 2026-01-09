<?php

namespace Modules\Hostels\Filament\Resources\HostelHousekeepingResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelHousekeepingResource;

class ListHostelHousekeepings extends ListRecords
{
    protected static string $resource = HostelHousekeepingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
