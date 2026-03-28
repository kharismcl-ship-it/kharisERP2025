<?php

namespace Modules\Farms\Filament\Resources\FarmComplianceChecklistResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmComplianceChecklistResource;

class ListFarmComplianceChecklists extends ListRecords
{
    protected static string $resource = FarmComplianceChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}