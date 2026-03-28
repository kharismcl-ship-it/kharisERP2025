<?php

namespace Modules\Farms\Filament\Resources\FarmCooperativeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmCooperativeResource;

class ViewFarmCooperative extends ViewRecord
{
    protected static string $resource = FarmCooperativeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }
}
