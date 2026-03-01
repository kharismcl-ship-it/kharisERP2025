<?php

namespace Modules\Farms\Filament\Resources\FarmTaskResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmTaskResource;

class ListFarmTasks extends ListRecords
{
    protected static string $resource = FarmTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}