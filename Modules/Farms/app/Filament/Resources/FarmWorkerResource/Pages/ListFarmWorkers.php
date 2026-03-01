<?php

namespace Modules\Farms\Filament\Resources\FarmWorkerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmWorkerResource;

class ListFarmWorkers extends ListRecords
{
    protected static string $resource = FarmWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
