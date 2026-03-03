<?php

namespace Modules\Construction\Filament\Resources\ConstructionDocumentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ConstructionDocumentResource;

class EditConstructionDocument extends EditRecord
{
    protected static string $resource = ConstructionDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
