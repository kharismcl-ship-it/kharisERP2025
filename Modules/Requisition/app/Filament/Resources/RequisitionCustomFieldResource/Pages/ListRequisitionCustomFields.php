<?php

namespace Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource;

class ListRequisitionCustomFields extends ListRecords
{
    protected static string $resource = RequisitionCustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}