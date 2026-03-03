<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource;

class ListMwTreatmentStages extends ListRecords
{
    protected static string $resource = MwTreatmentStageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
