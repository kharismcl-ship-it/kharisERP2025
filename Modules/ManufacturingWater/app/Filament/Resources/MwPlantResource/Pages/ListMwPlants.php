<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource;

class ListMwPlants extends ListRecords
{
    protected static string $resource = MwPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
