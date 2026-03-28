<?php

namespace Modules\Farms\Filament\Resources\FarmLaborPayrollResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmLaborPayrollResource;

class EditFarmLaborPayroll extends EditRecord
{
    protected static string $resource = FarmLaborPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}