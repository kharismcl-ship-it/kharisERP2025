<?php

namespace Modules\Construction\Filament\Resources\WorkerAttendanceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\WorkerAttendanceResource;

class ViewWorkerAttendance extends ViewRecord
{
    protected static string $resource = WorkerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
