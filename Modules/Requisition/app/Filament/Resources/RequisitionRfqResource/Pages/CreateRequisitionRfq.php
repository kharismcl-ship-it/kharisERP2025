<?php

namespace Modules\Requisition\Filament\Resources\RequisitionRfqResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionRfqResource;

class CreateRequisitionRfq extends CreateRecord
{
    protected static string $resource = RequisitionRfqResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id']          = filament()->getTenant()?->getKey();
        $data['created_by_user_id']  = auth()->id();
        return $data;
    }
}