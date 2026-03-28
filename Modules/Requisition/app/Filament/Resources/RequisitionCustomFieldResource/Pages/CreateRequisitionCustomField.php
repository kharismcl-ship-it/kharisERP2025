<?php

namespace Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource;

class CreateRequisitionCustomField extends CreateRecord
{
    protected static string $resource = RequisitionCustomFieldResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->getKey();
        return $data;
    }
}