<?php

namespace Modules\HR\Filament\Resources\EmployeeBenefitResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\EmployeeBenefitResource;

class EditEmployeeBenefit extends EditRecord
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
