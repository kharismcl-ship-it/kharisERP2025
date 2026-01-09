<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommMessageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\CommunicationCentre\Filament\Resources\CommMessageResource;

class EditCommMessage extends EditRecord
{
    protected static string $resource = CommMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
