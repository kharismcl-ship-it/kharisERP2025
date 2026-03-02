<?php

namespace Modules\HR\Filament\Resources\SalaryScaleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\SalaryScaleResource;
use Filament\Actions\CreateAction;

class ListSalaryScales extends ListRecords
{
    protected static string $resource = SalaryScaleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
