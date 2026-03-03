<?php

namespace Modules\Construction\Filament\Resources\MaterialUsageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\MaterialUsageResource;

class EditMaterialUsage extends EditRecord
{
    protected static string $resource = MaterialUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
