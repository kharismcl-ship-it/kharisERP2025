<?php

namespace Modules\Requisition\Filament\Resources\RequisitionScheduleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionScheduleResource;

class ListRequisitionSchedules extends ListRecords
{
    protected static string $resource = RequisitionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}