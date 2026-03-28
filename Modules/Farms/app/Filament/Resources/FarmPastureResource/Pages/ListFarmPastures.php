<?php

namespace Modules\Farms\Filament\Resources\FarmPastureResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmPastureResource;

class ListFarmPastures extends ListRecords
{
    protected static string $resource = FarmPastureResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}