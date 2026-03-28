<?php

namespace Modules\Farms\Filament\Resources\FarmCarbonResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmCarbonResource;

class EditFarmCarbonRecord extends EditRecord
{
    protected static string $resource = FarmCarbonResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}