<?php
namespace Modules\Farms\Filament\Resources\FarmProduceInventoryResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmProduceInventoryResource;
class ListFarmProduceInventories extends ListRecords {
    protected static string $resource = FarmProduceInventoryResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
