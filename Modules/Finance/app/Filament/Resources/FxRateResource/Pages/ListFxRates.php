<?php

namespace Modules\Finance\Filament\Resources\FxRateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\FxRateResource;

class ListFxRates extends ListRecords
{
    protected static string $resource = FxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
