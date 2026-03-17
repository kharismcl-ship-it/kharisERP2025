<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource;
use Modules\Farms\Models\FarmWorker;
use Modules\HR\Models\Employee;

class CreateMyFarmDailyReport extends CreateRecord
{
    protected static string $resource = MyFarmDailyReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if ($employee) {
            $worker = FarmWorker::where('employee_id', $employee->id)
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->first();

            if ($worker) {
                $data['farm_worker_id'] = $worker->id;
                $data['farm_id']        = $worker->farm_id;
            }
        }

        $data['company_id'] = $companyId;
        $data['status']     = 'draft';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
