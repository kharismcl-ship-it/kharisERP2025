<?php
namespace Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource;
class ListVendorCatalogs extends ListRecords {
    protected static string $resource = VendorCatalogResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
