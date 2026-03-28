<?php

namespace Modules\Farms\Filament\Resources\FarmRotationPlanResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmRotationPlanResource;

class ListFarmRotationPlans extends ListRecords
{
    protected static string $resource = FarmRotationPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}