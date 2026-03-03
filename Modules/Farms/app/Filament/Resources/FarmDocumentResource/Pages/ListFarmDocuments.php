<?php

namespace Modules\Farms\Filament\Resources\FarmDocumentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmDocumentResource;

class ListFarmDocuments extends ListRecords
{
    protected static string $resource = FarmDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
