<?php

namespace Modules\Farms\Filament\Resources\FarmComplianceChecklistResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmComplianceChecklistResource;

class EditFarmComplianceChecklist extends EditRecord
{
    protected static string $resource = FarmComplianceChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}