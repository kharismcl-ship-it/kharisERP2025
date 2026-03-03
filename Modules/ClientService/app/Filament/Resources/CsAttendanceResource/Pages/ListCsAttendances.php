<?php

namespace Modules\ClientService\Filament\Resources\CsAttendanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ClientService\Filament\Resources\CsAttendanceResource;

class ListCsAttendances extends ListRecords
{
    protected static string $resource = CsAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
