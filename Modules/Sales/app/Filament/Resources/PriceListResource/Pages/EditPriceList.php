<?php

namespace Modules\Sales\Filament\Resources\PriceListResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\PriceListResource;

class EditPriceList extends EditRecord
{
    protected static string $resource = PriceListResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
