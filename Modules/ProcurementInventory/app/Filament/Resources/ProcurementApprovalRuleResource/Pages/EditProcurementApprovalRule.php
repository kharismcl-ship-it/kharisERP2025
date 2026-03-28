<?php
namespace Modules\ProcurementInventory\Filament\Resources\ProcurementApprovalRuleResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ProcurementInventory\Filament\Resources\ProcurementApprovalRuleResource;
class EditProcurementApprovalRule extends EditRecord {
    protected static string $resource = ProcurementApprovalRuleResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
