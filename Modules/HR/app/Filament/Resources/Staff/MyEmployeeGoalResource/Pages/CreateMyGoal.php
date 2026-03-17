<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource;
use Modules\HR\Models\Employee;

class CreateMyGoal extends CreateRecord
{
    protected static string $resource = MyEmployeeGoalResource::class;

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

        $data['employee_id'] = $employee->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
