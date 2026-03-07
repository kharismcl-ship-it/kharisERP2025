<?php

namespace Modules\Requisition\Filament\Resources\RequisitionTemplateResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionTemplateResource;

class EditRequisitionTemplate extends EditRecord
{
    protected static string $resource = RequisitionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}