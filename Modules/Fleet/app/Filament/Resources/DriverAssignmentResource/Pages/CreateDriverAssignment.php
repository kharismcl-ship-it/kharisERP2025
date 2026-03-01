<?php

namespace Modules\Fleet\Filament\Resources\DriverAssignmentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fleet\Filament\Resources\DriverAssignmentResource;

class CreateDriverAssignment extends CreateRecord
{
    protected static string $resource = DriverAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
