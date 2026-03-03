<?php

namespace Modules\Farms\Filament\Resources\FarmRequestResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmRequestResource;

class ViewFarmRequest extends ViewRecord
{
    protected static string $resource = FarmRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
