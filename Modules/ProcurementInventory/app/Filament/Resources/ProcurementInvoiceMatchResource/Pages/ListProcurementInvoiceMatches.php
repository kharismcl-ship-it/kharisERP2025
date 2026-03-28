<?php
namespace Modules\ProcurementInventory\Filament\Resources\ProcurementInvoiceMatchResource\Pages;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\ProcurementInvoiceMatchResource;
class ListProcurementInvoiceMatches extends ListRecords {
    protected static string $resource = ProcurementInvoiceMatchResource::class;
    protected function getHeaderActions(): array { return []; }
}
