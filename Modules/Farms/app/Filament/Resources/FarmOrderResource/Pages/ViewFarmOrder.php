<?php

namespace Modules\Farms\Filament\Resources\FarmOrderResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmOrderResource;

class ViewFarmOrder extends ViewRecord
{
    protected static string $resource = FarmOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Update Status'),
        ];
    }
}
