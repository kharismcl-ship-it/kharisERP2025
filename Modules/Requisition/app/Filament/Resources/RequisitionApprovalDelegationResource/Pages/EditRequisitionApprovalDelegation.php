<?php

namespace Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource;

class EditRequisitionApprovalDelegation extends EditRecord
{
    protected static string $resource = RequisitionApprovalDelegationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}