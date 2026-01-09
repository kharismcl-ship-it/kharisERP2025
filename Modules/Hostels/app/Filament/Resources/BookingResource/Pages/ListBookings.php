<?php

namespace Modules\Hostels\Filament\Resources\BookingResource\Pages;

    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;
    use Modules\Hostels\Filament\Resources\BookingResource;

    class ListBookings extends ListRecords {
        protected static string $resource = BookingResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
