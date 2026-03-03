<?php

namespace Modules\Construction\Filament\Resources\ConstructionWorkerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ConstructionWorkerResource;

class ListConstructionWorkers extends ListRecords
{
    protected static string $resource = ConstructionWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
