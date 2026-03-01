<?php

namespace Modules\Farms\Filament\Resources\CropScoutingResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\CropScoutingResource;

class EditCropScoutingRecord extends EditRecord
{
    protected static string $resource = CropScoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}