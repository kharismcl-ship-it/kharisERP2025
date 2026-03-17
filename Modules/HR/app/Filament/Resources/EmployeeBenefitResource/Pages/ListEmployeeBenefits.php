<?php

namespace Modules\HR\Filament\Resources\EmployeeBenefitResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\EmployeeBenefitResource;

class ListEmployeeBenefits extends ListRecords
{
    protected static string $resource = EmployeeBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
