<?php

declare(strict_types=1);

namespace Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource;

class ListRequisitionWorkflowRules extends ListRecords
{
    protected static string $resource = RequisitionWorkflowRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
