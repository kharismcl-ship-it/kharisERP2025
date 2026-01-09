<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\BookingChangeRequestResource;

class CreateBookingChangeRequest extends CreateRecord
{
    protected static string $resource = BookingChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
