<?php
namespace Modules\ProcurementInventory\Filament\Resources\VendorPerformanceResource\Pages;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\VendorPerformanceResource;
class ListVendorPerformances extends ListRecords {
    protected static string $resource = VendorPerformanceResource::class;
    protected function getHeaderActions(): array { return []; }
}
