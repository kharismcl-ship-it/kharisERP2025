<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommProviderConfigResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\CommunicationCentre\Filament\Resources\CommProviderConfigResource;

class ListCommProviderConfigs extends ListRecords
{
    protected static string $resource = CommProviderConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
