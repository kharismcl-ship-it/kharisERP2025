<?php

namespace Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource;

class CreateRequisitionReminderRule extends CreateRecord
{
    protected static string $resource = RequisitionReminderRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->getKey();
        return $data;
    }
}