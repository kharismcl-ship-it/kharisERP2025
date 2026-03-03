<?php

namespace Modules\Construction\Filament\Resources\ConstructionWorkerResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ConstructionWorkerResource;

class EditConstructionWorker extends EditRecord
{
    protected static string $resource = ConstructionWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
