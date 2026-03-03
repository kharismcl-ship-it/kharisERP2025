<?php

namespace Modules\Construction\Filament\Resources\WorkerAttendanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\WorkerAttendanceResource;

class ListWorkerAttendances extends ListRecords
{
    protected static string $resource = WorkerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
