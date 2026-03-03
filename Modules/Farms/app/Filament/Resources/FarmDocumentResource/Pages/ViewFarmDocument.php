<?php

namespace Modules\Farms\Filament\Resources\FarmDocumentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmDocumentResource;

class ViewFarmDocument extends ViewRecord
{
    protected static string $resource = FarmDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
