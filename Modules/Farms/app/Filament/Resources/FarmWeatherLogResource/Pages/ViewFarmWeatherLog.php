<?php
namespace Modules\Farms\Filament\Resources\FarmWeatherLogResource\Pages;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmWeatherLogResource;
class ViewFarmWeatherLog extends ViewRecord {
    protected static string $resource = FarmWeatherLogResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
}
