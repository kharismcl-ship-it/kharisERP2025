<?php

namespace Modules\Sales\Filament\Resources\CatalogItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\CatalogItemResource;

class EditCatalogItem extends EditRecord
{
    protected static string $resource = CatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
