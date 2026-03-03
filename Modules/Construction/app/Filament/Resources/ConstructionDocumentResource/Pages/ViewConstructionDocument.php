<?php

namespace Modules\Construction\Filament\Resources\ConstructionDocumentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ConstructionDocumentResource;

class ViewConstructionDocument extends ViewRecord
{
    protected static string $resource = ConstructionDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
