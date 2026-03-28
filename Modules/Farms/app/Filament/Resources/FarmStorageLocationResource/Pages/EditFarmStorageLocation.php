<?php

namespace Modules\Farms\Filament\Resources\FarmStorageLocationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmStorageLocationResource;

class EditFarmStorageLocation extends EditRecord
{
    protected static string $resource = FarmStorageLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}