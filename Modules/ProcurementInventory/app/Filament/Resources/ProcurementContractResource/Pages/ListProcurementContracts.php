<?php
namespace Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource;
class ListProcurementContracts extends ListRecords {
    protected static string $resource = ProcurementContractResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
