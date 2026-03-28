<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Modules\HR\Models\Employee;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Services\RequisitionPolicyEnforcementService;

class CreateMyRequisition extends CreateRecord
{
    protected static string $resource = MyRequisitionResource::class;

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
        $data['target_company_id']     = $companyId;
        $data['requester_employee_id'] = $employee->id;
        $data['status']                = 'submitted';

        // Auto-fill department if not selected by staff
        if (empty($data['target_department_id'])) {
            $data['target_department_id'] = $employee->department_id;
        }

        // Run spend policy checks using a temporary unsaved model
        $enforcement = app(RequisitionPolicyEnforcementService::class);
        $tempReq     = new Requisition($data);

        $budgetError = $enforcement->checkBudget($tempReq);
        if ($budgetError) {
            throw ValidationException::withMessages(['total_estimated_cost' => $budgetError]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
