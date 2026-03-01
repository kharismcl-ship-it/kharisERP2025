<?php

namespace Modules\Farms\Filament\Resources\CropActivityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\CropActivityResource;

class EditCropActivity extends EditRecord
{
    protected static string $resource = CropActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}