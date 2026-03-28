<?php

namespace Modules\Farms\Filament\Resources\FarmCooperativeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmCooperativeResource;

class ListFarmCooperatives extends ListRecords
{
    protected static string $resource = FarmCooperativeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
