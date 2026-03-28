<?php

namespace Modules\Farms\Filament\Resources\FarmProduceLotResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmProduceLotResource;

class ViewFarmProduceLot extends ViewRecord
{
    protected static string $resource = FarmProduceLotResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}