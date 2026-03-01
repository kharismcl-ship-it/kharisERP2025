<?php

namespace Modules\Sales\Filament\Resources\PosSaleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\PosSaleResource;

class ListPosSales extends ListRecords
{
    protected static string $resource = PosSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
