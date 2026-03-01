<?php
namespace Modules\Farms\Filament\Resources\LivestockEventResource\Pages;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\LivestockEventResource;
class ViewLivestockEvent extends ViewRecord {
    protected static string $resource = LivestockEventResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
}
