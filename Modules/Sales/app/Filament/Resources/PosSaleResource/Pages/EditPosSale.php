<?php

namespace Modules\Sales\Filament\Resources\PosSaleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\PosSaleResource;

class EditPosSale extends EditRecord
{
    protected static string $resource = PosSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
