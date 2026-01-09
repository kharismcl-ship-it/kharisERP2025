<?php

namespace Modules\Hostels\Filament\Resources\HostelBlockResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelBlockResource;

class ListHostelBlocks extends ListRecords
{
    protected static string $resource = HostelBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
