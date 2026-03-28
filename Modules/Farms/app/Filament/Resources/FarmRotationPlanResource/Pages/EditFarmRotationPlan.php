<?php

namespace Modules\Farms\Filament\Resources\FarmRotationPlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmRotationPlanResource;

class EditFarmRotationPlan extends EditRecord
{
    protected static string $resource = FarmRotationPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}