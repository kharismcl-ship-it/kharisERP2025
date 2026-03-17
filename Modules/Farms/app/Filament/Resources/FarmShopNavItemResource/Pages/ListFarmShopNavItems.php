<?php

namespace Modules\Farms\Filament\Resources\FarmShopNavItemResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmShopNavItemResource;

class ListFarmShopNavItems extends ListRecords
{
    protected static string $resource = FarmShopNavItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
