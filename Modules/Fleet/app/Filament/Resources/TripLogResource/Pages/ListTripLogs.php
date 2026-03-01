<?php

namespace Modules\Fleet\Filament\Resources\TripLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Fleet\Filament\Resources\TripLogResource;

class ListTripLogs extends ListRecords
{
    protected static string $resource = TripLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
