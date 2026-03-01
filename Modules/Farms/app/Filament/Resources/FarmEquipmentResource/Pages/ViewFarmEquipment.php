<?php
namespace Modules\Farms\Filament\Resources\FarmEquipmentResource\Pages;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmEquipmentResource;
class ViewFarmEquipment extends ViewRecord {
    protected static string $resource = FarmEquipmentResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
}
