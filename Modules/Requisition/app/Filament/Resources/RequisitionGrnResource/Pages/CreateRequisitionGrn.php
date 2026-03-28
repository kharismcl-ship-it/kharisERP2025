e<?php

namespace Modules\Requisition\Filament\Resources\RequisitionGrnResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionGrnResource;

class CreateRequisitionGrn extends CreateRecord
{
    protected static string $resource = RequisitionGrnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = filament()->getTenant()?->getKey();
        return $data;
    }
}