<?php

namespace Modules\Farms\Filament\Resources\FarmTrialResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmTrialResource;

class ListFarmTrials extends ListRecords
{
    protected static string $resource = FarmTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}