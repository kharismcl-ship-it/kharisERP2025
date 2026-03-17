<?php

namespace Modules\Farms\Filament\Resources\FarmReturnRequestResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmReturnRequestResource;

class ViewFarmReturnRequest extends ViewRecord
{
    protected static string $resource = FarmReturnRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Process Request'),
        ];
    }
}
