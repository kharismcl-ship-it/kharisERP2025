<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwChemicalUsageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingWater\Filament\Resources\MwChemicalUsageResource;

class ListMwChemicalUsages extends ListRecords
{
    protected static string $resource = MwChemicalUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
