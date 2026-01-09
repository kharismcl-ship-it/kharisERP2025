<?php

namespace Modules\Hostels\Filament\Resources\RoomResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\RoomResource;

    class CreateRoom extends CreateRecord {
        protected static string $resource = RoomResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
