<?php

namespace Modules\Farms\Filament\Resources\FarmNdviResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmNdviResource;

class ListFarmNdviRecords extends ListRecords
{
    protected static string $resource = FarmNdviResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}