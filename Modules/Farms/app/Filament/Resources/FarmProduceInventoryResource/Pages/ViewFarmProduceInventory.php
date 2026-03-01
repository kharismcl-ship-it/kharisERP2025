<?php
namespace Modules\Farms\Filament\Resources\FarmProduceInventoryResource\Pages;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmProduceInventoryResource;
class ViewFarmProduceInventory extends ViewRecord {
    protected static string $resource = FarmProduceInventoryResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
}
