<?php

namespace Modules\Farms\Filament\Resources\FarmSaleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmSaleResource;

class ListFarmSales extends ListRecords
{
    protected static string $resource = FarmSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
