<?php

namespace Modules\Fleet\Filament\Resources\MaintenanceRecordResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fleet\Filament\Resources\MaintenanceRecordResource;

class CreateMaintenanceRecord extends CreateRecord
{
    protected static string $resource = MaintenanceRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
