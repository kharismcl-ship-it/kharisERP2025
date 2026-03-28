<?php
namespace Modules\ProcurementInventory\Filament\Resources\ProcurementApprovalRuleResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\ProcurementApprovalRuleResource;
class ListProcurementApprovalRules extends ListRecords {
    protected static string $resource = ProcurementApprovalRuleResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
