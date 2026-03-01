<?php

namespace Modules\HR\Filament\Resources\BenefitTypeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\BenefitTypeResource;

class ListBenefitTypes extends ListRecords
{
    protected static string $resource = BenefitTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
        ];
    }
}