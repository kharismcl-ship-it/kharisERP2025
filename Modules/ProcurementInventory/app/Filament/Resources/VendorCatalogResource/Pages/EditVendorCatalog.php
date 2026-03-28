<?php
namespace Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource;
class EditVendorCatalog extends EditRecord {
    protected static string $resource = VendorCatalogResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
