<?php

namespace Modules\Construction\Filament\Resources\ConstructionDocumentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ConstructionDocumentResource;

class ListConstructionDocuments extends ListRecords
{
    protected static string $resource = ConstructionDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
