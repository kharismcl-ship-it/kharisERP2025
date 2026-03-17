<?php

namespace Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource;
use Modules\HR\Models\Employee;

class CreateMyItRequest extends CreateRecord
{
    protected static string $resource = MyItRequestResource::class;

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

        $data['company_id']            = $companyId;
        $data['requester_employee_id'] = $employee->id;
        $data['status']                = 'open';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
