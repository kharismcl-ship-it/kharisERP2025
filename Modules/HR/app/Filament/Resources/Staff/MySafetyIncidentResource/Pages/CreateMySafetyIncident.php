<?php

namespace Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource;
use Modules\HR\Models\Employee;

class CreateMySafetyIncident extends CreateRecord
{
    protected static string $resource = MySafetyIncidentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)->first();

        $data['company_id']              = $companyId;
        $data['reported_by_employee_id'] = $employee?->id;
        $data['status']                  = 'open';

        return $data;
    }
}