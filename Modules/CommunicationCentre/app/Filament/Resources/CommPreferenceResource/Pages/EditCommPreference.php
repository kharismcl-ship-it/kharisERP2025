<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommPreferenceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\CommunicationCentre\Filament\Resources\CommPreferenceResource;

class EditCommPreference extends EditRecord
{
    protected static string $resource = CommPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
