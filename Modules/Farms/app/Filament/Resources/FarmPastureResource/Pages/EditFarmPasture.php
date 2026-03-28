<?php

namespace Modules\Farms\Filament\Resources\FarmPastureResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmPastureResource;

class EditFarmPasture extends EditRecord
{
    protected static string $resource = FarmPastureResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}