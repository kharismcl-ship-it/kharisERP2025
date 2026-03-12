<?php

namespace Modules\Hostels\Filament\Resources\HostelBookResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelBookResource;

class ListHostelBooks extends ListRecords
{
    protected static string $resource = HostelBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
