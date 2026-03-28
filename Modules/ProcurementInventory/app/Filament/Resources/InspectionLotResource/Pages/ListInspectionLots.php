<?php
namespace Modules\ProcurementInventory\Filament\Resources\InspectionLotResource\Pages;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\InspectionLotResource;
class ListInspectionLots extends ListRecords {
    protected static string $resource = InspectionLotResource::class;
    protected function getHeaderActions(): array { return []; }
}
