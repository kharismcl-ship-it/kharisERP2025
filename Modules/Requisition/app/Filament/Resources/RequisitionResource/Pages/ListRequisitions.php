<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionResource;

class ListRequisitions extends ListRecords
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
