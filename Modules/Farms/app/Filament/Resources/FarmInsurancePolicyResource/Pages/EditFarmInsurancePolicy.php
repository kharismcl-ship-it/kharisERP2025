<?php

namespace Modules\Farms\Filament\Resources\FarmInsurancePolicyResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmInsurancePolicyResource;

class EditFarmInsurancePolicy extends EditRecord
{
    protected static string $resource = FarmInsurancePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}