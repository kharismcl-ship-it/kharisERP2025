<?php

namespace Modules\Hostels\Filament\Resources\HostelStaffAttendanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelStaffAttendanceResource;

class ListHostelStaffAttendances extends ListRecords
{
    protected static string $resource = HostelStaffAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
