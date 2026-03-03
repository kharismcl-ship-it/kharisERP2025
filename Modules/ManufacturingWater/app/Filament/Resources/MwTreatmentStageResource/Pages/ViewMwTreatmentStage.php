<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource;

class ViewMwTreatmentStage extends ViewRecord
{
    protected static string $resource = MwTreatmentStageResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
