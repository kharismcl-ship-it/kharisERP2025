<?php
namespace Modules\Farms\Filament\Resources\FarmEquipmentResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmEquipmentResource;
class ListFarmEquipment extends ListRecords {
    protected static string $resource = FarmEquipmentResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
