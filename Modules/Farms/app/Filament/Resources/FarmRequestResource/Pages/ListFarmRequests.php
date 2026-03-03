<?php

namespace Modules\Farms\Filament\Resources\FarmRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmRequestResource;

class ListFarmRequests extends ListRecords
{
    protected static string $resource = FarmRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
