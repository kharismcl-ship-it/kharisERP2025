<?php

namespace Modules\Farms\Filament\Resources\FarmWorkerAttendanceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmWorkerAttendanceResource;

class ViewFarmWorkerAttendance extends ViewRecord
{
    protected static string $resource = FarmWorkerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
