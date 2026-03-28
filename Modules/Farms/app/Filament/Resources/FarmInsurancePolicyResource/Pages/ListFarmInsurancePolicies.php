<?php

namespace Modules\Farms\Filament\Resources\FarmInsurancePolicyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmInsurancePolicyResource;

class ListFarmInsurancePolicies extends ListRecords
{
    protected static string $resource = FarmInsurancePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}