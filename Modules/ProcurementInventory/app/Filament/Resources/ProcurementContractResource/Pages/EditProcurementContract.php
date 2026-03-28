<?php
namespace Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource;
class EditProcurementContract extends EditRecord {
    protected static string $resource = ProcurementContractResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
