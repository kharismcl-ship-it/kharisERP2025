<?php

namespace Modules\Farms\Filament\Resources\FarmSmsCommandResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmSmsCommandResource;

class ListFarmSmsCommands extends ListRecords
{
    protected static string $resource = FarmSmsCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}