<?php

namespace Modules\Farms\Filament\Resources\FarmB2bAccountResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmB2bAccountResource;

class ViewFarmB2bAccount extends ViewRecord
{
    protected static string $resource = FarmB2bAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
