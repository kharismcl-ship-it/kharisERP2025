<?php

namespace Modules\Requisition\Filament\Resources\RequisitionGrnResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionGrnResource;

class ListRequisitionGrns extends ListRecords
{
    protected static string $resource = RequisitionGrnResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}