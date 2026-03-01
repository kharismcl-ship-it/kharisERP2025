<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ConstructionProjectResource;

class EditConstructionProject extends EditRecord
{
    protected static string $resource = ConstructionProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
