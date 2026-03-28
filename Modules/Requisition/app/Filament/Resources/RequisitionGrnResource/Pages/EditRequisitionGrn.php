<?php

namespace Modules\Requisition\Filament\Resources\RequisitionGrnResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionGrnResource;

class EditRequisitionGrn extends EditRecord
{
    protected static string $resource = RequisitionGrnResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}