<?php

namespace Modules\CommunicationCentre\Filament\Resources\CommTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\CommunicationCentre\Filament\Resources\CommTemplateResource;

class ListCommTemplates extends ListRecords
{
    protected static string $resource = CommTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
