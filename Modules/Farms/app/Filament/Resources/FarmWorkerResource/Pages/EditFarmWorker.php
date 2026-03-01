<?php

namespace Modules\Farms\Filament\Resources\FarmWorkerResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmWorkerResource;

class EditFarmWorker extends EditRecord
{
    protected static string $resource = FarmWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}