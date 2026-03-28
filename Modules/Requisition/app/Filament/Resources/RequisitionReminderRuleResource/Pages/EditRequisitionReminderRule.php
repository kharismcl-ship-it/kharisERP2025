<?php

namespace Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource;

class EditRequisitionReminderRule extends EditRecord
{
    protected static string $resource = RequisitionReminderRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}