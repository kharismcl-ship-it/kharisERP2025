<?php
namespace Modules\Farms\Filament\Resources\FarmProduceInventoryResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmProduceInventoryResource;
class EditFarmProduceInventory extends EditRecord {
    protected static string $resource = FarmProduceInventoryResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
