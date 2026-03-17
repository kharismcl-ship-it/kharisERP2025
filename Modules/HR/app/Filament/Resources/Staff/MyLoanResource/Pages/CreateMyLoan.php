<?php

namespace Modules\HR\Filament\Resources\Staff\MyLoanResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\Staff\MyLoanResource;
use Modules\HR\Models\Employee;

class CreateMyLoan extends CreateRecord
{
    protected static string $resource = MyLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            Notification::make()
                ->title('No employee profile found')
                ->body('Your account is not linked to an employee record for this company. Please contact HR.')
                ->danger()
                ->send();
            $this->halt();
        }

        $data['employee_id']         = $employee->id;
        $data['company_id']          = $companyId;
        $data['status']              = 'pending';
        $data['outstanding_balance'] = $data['principal_amount'];

        if (! empty($data['repayment_months']) && $data['principal_amount'] > 0) {
            $data['monthly_deduction'] = round($data['principal_amount'] / $data['repayment_months'], 2);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
