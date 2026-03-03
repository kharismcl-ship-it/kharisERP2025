<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwChemicalUsageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingWater\Filament\Resources\MwChemicalUsageResource;

class EditMwChemicalUsage extends EditRecord
{
    protected static string $resource = MwChemicalUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
