<?php

namespace Modules\Farms\Filament\Resources\FarmCarbonResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmCarbonResource;

class ListFarmCarbonRecords extends ListRecords
{
    protected static string $resource = FarmCarbonResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}