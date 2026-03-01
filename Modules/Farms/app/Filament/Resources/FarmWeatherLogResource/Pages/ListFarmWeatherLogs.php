<?php
namespace Modules\Farms\Filament\Resources\FarmWeatherLogResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmWeatherLogResource;
class ListFarmWeatherLogs extends ListRecords {
    protected static string $resource = FarmWeatherLogResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
