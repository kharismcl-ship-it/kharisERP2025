<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommPreferenceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\CommunicationCentre\Filament\Resources\CommPreferenceResource;

class ListCommPreferences extends ListRecords
{
    protected static string $resource = CommPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
