<?php
namespace Modules\ProcurementInventory\Filament\Resources\RtvOrderResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\RtvOrderResource;
class ListRtvOrders extends ListRecords {
    protected static string $resource = RtvOrderResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
