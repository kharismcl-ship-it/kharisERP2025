<?php

namespace Modules\HR\Filament\Resources\EmploymentContractResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\EmploymentContractResource;
use Filament\Actions\CreateAction;

class ListEmploymentContracts extends ListRecords
{
    protected static string $resource = EmploymentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
