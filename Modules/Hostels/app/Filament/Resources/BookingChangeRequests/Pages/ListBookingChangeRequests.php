<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\BookingChangeRequestResource;

class ListBookingChangeRequests extends ListRecords
{
    protected static string $resource = BookingChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
