<?php

namespace Modules\Farms\Filament\Resources\FarmShopPageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmShopPageResource;

class ListFarmShopPages extends ListRecords
{
    protected static string $resource = FarmShopPageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
