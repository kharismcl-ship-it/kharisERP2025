<?php

namespace Modules\Sales\Filament\Resources\PriceListResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\PriceListResource;

class ListPriceLists extends ListRecords
{
    protected static string $resource = PriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
