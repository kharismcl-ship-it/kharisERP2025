<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource;

class ViewMwPlant extends ViewRecord
{
    protected static string $resource = MwPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
