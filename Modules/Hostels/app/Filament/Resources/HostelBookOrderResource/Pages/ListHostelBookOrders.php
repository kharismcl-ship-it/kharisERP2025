<?php

namespace Modules\Hostels\Filament\Resources\HostelBookOrderResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelBookOrderResource;

class ListHostelBookOrders extends ListRecords
{
    protected static string $resource = HostelBookOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
