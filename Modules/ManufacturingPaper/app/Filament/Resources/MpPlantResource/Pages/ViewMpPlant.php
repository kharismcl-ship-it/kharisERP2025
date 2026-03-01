<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpPlantResource;

class ViewMpPlant extends ViewRecord
{
    protected static string $resource = MpPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
