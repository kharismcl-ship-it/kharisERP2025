<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionResource;

class EditRequisition extends EditRecord
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
