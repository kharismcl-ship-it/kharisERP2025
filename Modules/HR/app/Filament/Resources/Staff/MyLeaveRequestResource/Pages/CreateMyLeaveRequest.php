<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource;
use Modules\HR\Models\Employee;
use Modules\HR\Services\LeaveApprovalService;

class CreateMyLeaveRequest extends CreateRecord
{
    protected static string $resource = MyLeaveRequestResource::class;

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

        $data['employee_id'] = $employee->id;
        $data['company_id']  = $companyId;
        $data['status']      = 'pending';
        $data['total_days']  = \Carbon\Carbon::parse($data['start_date'])->diffInDays(\Carbon\Carbon::parse($data['end_date'])) + 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        app(LeaveApprovalService::class)->initializeApprovalProcess($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
