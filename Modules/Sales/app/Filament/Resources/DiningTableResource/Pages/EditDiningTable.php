<?php

namespace Modules\Sales\Filament\Resources\DiningTableResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\DiningTableResource;

class EditDiningTable extends EditRecord
{
    protected static string $resource = DiningTableResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
