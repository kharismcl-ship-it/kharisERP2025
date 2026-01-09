<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommMessageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\CommunicationCentre\Filament\Resources\CommMessageResource;

class ListCommMessages extends ListRecords
{
    protected static string $resource = CommMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
