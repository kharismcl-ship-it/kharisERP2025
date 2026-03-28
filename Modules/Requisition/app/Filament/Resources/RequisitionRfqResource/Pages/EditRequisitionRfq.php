<?php

namespace Modules\Requisition\Filament\Resources\RequisitionRfqResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionRfqResource;

class EditRequisitionRfq extends EditRecord
{
    protected static string $resource = RequisitionRfqResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}