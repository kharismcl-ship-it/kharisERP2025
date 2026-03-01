<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource;

class EditMwPlant extends EditRecord
{
    protected static string $resource = MwPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
