<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommProviderConfigResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\CommunicationCentre\Filament\Resources\CommProviderConfigResource;

class EditCommProviderConfig extends EditRecord
{
    protected static string $resource = CommProviderConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
