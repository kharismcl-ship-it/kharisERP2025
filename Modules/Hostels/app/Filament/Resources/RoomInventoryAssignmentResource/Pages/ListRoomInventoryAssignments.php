<?php

namespace Modules\Hostels\Filament\Resources\RoomInventoryAssignmentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\RoomInventoryAssignmentResource;

class ListRoomInventoryAssignments extends ListRecords
{
    protected static string $resource = RoomInventoryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
