<?php

namespace Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\Staff\MyGrievanceResource;
use Modules\HR\Models\Employee;

class CreateMyGrievance extends CreateRecord
{
    protected static string $resource = MyGrievanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            Notification::make()->title('No employee profile found')->body('Your user account is not linked to an employee record for this company. Please contact HR.')->danger()->send();
            $this->halt();
        }

        $data['company_id']  = $companyId;
        $data['employee_id'] = $employee->id;
        $data['filed_date']  = now()->toDateString();
        $data['status']      = 'filed';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
