<?php

namespace Modules\HR\Filament\Resources\SafetyInspectionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\SafetyInspectionResource;

class ListSafetyInspections extends ListRecords
{
    protected static string $resource = SafetyInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}