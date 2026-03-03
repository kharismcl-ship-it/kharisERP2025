<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource;

class EditMwTreatmentStage extends EditRecord
{
    protected static string $resource = MwTreatmentStageResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
