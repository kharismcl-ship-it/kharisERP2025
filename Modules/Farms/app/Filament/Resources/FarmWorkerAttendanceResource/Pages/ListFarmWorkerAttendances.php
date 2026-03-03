<?php

namespace Modules\Farms\Filament\Resources\FarmWorkerAttendanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmWorkerAttendanceResource;

class ListFarmWorkerAttendances extends ListRecords
{
    protected static string $resource = FarmWorkerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
