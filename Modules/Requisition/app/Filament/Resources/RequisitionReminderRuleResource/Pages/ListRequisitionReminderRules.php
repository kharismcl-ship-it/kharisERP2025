<?php

namespace Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource;

class ListRequisitionReminderRules extends ListRecords
{
    protected static string $resource = RequisitionReminderRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}