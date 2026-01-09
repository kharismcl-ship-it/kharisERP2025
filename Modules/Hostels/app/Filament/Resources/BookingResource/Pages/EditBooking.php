<?php

namespace Modules\Hostels\Filament\Resources\BookingResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\BookingResource;

    class EditBooking extends EditRecord {
        protected static string $resource = BookingResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
