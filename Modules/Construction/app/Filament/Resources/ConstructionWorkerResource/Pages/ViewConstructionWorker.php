<?php

namespace Modules\Construction\Filament\Resources\ConstructionWorkerResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ConstructionWorkerResource;

class ViewConstructionWorker extends ViewRecord
{
    protected static string $resource = ConstructionWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
