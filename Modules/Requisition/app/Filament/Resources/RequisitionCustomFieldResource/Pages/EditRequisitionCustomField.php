<?php

namespace Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource;

class EditRequisitionCustomField extends EditRecord
{
    protected static string $resource = RequisitionCustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}