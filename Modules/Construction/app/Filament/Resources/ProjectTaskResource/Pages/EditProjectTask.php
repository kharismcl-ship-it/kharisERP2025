<?php

namespace Modules\Construction\Filament\Resources\ProjectTaskResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ProjectTaskResource;

class EditProjectTask extends EditRecord
{
    protected static string $resource = ProjectTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
