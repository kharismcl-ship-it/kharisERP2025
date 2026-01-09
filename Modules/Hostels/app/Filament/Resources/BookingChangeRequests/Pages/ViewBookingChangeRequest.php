<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\BookingChangeRequestResource;

class ViewBookingChangeRequest extends ViewRecord
{
    protected static string $resource = BookingChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
            EditAction::make(),
        ];
    }
}
