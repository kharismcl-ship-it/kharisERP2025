<?php

namespace Modules\Fleet\Filament\Resources\DriverAssignmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Fleet\Filament\Resources\DriverAssignmentResource;

class ListDriverAssignments extends ListRecords
{
    protected static string $resource = DriverAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
