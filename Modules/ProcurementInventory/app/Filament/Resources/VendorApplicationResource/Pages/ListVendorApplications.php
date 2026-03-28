<?php
namespace Modules\ProcurementInventory\Filament\Resources\VendorApplicationResource\Pages;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\VendorApplicationResource;
class ListVendorApplications extends ListRecords {
    protected static string $resource = VendorApplicationResource::class;
    protected function getHeaderActions(): array { return []; }
}
