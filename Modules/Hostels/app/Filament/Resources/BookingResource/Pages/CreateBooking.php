<?php

namespace Modules\Hostels\Filament\Resources\BookingResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\BookingResource;

    class CreateBooking extends CreateRecord {
        protected static string $resource = BookingResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
