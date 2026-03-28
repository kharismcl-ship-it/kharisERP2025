<?php

namespace Modules\Requisition\Filament\Resources\RequisitionScheduleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionScheduleResource;

class CreateRequisitionSchedule extends CreateRecord
{
    protected static string $resource = RequisitionScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->getKey();
        return $data;
    }
}