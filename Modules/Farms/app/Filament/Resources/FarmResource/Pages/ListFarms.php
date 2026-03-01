<?php

namespace Modules\Farms\Filament\Resources\FarmResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmResource;

class ListFarms extends ListRecords
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
