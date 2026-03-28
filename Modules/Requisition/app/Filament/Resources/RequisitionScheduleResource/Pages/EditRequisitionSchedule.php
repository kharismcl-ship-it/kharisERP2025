<?php

namespace Modules\Requisition\Filament\Resources\RequisitionScheduleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionScheduleResource;

class EditRequisitionSchedule extends EditRecord
{
    protected static string $resource = RequisitionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}