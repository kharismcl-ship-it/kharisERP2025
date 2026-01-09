<?php

namespace Modules\HR\Filament\Resources\AttendanceRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\AttendanceRecordResource;

class ListAttendanceRecords extends ListRecords
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
