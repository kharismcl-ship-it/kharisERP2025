<?php

namespace Modules\Farms\Filament\Resources\FarmDocumentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmDocumentResource;

class EditFarmDocument extends EditRecord
{
    protected static string $resource = FarmDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
