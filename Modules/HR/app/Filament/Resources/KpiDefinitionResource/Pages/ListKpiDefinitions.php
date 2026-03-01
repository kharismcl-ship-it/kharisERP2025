<?php

namespace Modules\HR\Filament\Resources\KpiDefinitionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\KpiDefinitionResource;

class ListKpiDefinitions extends ListRecords
{
    protected static string $resource = KpiDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
        ];
    }
}