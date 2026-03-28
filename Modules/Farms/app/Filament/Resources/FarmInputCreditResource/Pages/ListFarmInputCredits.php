<?php

namespace Modules\Farms\Filament\Resources\FarmInputCreditResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmInputCreditResource;

class ListFarmInputCredits extends ListRecords
{
    protected static string $resource = FarmInputCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}