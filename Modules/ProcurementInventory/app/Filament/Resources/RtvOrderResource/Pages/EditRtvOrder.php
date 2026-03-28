<?php
namespace Modules\ProcurementInventory\Filament\Resources\RtvOrderResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ProcurementInventory\Filament\Resources\RtvOrderResource;
class EditRtvOrder extends EditRecord {
    protected static string $resource = RtvOrderResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
