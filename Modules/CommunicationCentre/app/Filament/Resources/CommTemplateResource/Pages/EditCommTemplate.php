<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\CommunicationCentre\Filament\Resources\CommTemplateResource;

class EditCommTemplate extends EditRecord
{
    protected static string $resource = CommTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
