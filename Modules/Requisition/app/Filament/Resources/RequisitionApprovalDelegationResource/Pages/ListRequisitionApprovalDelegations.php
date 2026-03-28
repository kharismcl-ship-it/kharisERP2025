<?php

namespace Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource;

class ListRequisitionApprovalDelegations extends ListRecords
{
    protected static string $resource = RequisitionApprovalDelegationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}