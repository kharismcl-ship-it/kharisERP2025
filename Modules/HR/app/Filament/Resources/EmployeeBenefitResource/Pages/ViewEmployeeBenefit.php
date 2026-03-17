<?php

namespace Modules\HR\Filament\Resources\EmployeeBenefitResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\EmployeeBenefitResource;

class ViewEmployeeBenefit extends ViewRecord
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
