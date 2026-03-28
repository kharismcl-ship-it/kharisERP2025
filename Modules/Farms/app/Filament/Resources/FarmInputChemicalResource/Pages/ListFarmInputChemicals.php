<?php

namespace Modules\Farms\Filament\Resources\FarmInputChemicalResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmInputChemicalResource;

class ListFarmInputChemicals extends ListRecords
{
    protected static string $resource = FarmInputChemicalResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}