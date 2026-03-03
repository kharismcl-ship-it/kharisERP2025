<?php

namespace Modules\Construction\Filament\Resources\ProjectPhaseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ProjectPhaseResource;

class EditProjectPhase extends EditRecord
{
    protected static string $resource = ProjectPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
