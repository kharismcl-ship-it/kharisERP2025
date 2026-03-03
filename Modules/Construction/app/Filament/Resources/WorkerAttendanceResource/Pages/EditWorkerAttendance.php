<?php

namespace Modules\Construction\Filament\Resources\WorkerAttendanceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\WorkerAttendanceResource;

class EditWorkerAttendance extends EditRecord
{
    protected static string $resource = WorkerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
