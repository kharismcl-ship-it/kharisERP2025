<?php

namespace Modules\Fleet\Filament\Resources\TripLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Fleet\Filament\Resources\TripLogResource;

class EditTripLog extends EditRecord
{
    protected static string $resource = TripLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
