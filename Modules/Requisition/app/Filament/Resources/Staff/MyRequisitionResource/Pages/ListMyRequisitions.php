<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource;

class ListMyRequisitions extends ListRecords
{
    protected static string $resource = MyRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Requisition'),
        ];
    }
}
