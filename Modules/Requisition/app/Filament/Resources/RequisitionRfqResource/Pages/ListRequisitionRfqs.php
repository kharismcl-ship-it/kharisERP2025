<?php

namespace Modules\Requisition\Filament\Resources\RequisitionRfqResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionRfqResource;

class ListRequisitionRfqs extends ListRecords
{
    protected static string $resource = RequisitionRfqResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}