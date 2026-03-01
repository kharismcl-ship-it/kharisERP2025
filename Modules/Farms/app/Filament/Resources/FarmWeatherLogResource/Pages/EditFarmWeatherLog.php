<?php
namespace Modules\Farms\Filament\Resources\FarmWeatherLogResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmWeatherLogResource;
class EditFarmWeatherLog extends EditRecord {
    protected static string $resource = FarmWeatherLogResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
