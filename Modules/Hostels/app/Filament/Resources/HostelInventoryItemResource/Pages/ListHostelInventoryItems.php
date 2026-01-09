<?php

namespace Modules\Hostels\Filament\Resources\HostelInventoryItemResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelInventoryItemResource;

class ListHostelInventoryItems extends ListRecords
{
    protected static string $resource = HostelInventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
