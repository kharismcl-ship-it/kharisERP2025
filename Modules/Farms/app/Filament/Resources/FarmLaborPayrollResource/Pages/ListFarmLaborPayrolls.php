<?php

namespace Modules\Farms\Filament\Resources\FarmLaborPayrollResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmLaborPayrollResource;

class ListFarmLaborPayrolls extends ListRecords
{
    protected static string $resource = FarmLaborPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}