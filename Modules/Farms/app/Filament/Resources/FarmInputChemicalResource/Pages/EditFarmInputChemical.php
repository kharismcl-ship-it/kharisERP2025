<?php

namespace Modules\Farms\Filament\Resources\FarmInputChemicalResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmInputChemicalResource;

class EditFarmInputChemical extends EditRecord
{
    protected static string $resource = FarmInputChemicalResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}