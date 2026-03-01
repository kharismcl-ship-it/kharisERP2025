<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingPaper\Filament\Resources\MpPlantResource;

class ListMpPlants extends ListRecords
{
    protected static string $resource = MpPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
