<?php

namespace Modules\Hostels\Filament\Resources\RoomResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\RoomResource;

    class EditRoom extends EditRecord {
        protected static string $resource = RoomResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
