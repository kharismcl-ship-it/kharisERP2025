<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Requisition\Filament\Resources\RequisitionResource;

class ViewRequisition extends ViewRecord
{
    protected static string $resource = RequisitionResource::class;

    protected string $view = 'requisition::filament.resources.requisition-resource.pages.view-requisition';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
