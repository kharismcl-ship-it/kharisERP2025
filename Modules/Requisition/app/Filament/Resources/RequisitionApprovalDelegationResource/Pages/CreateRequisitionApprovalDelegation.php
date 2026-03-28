<?php

namespace Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource;

class CreateRequisitionApprovalDelegation extends CreateRecord
{
    protected static string $resource = RequisitionApprovalDelegationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->getKey();
        return $data;
    }
}