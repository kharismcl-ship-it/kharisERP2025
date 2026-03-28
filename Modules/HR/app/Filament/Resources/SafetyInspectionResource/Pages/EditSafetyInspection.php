<?php

namespace Modules\HR\Filament\Resources\SafetyInspectionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\SafetyInspectionResource;

class EditSafetyInspection extends EditRecord
{
    protected static string $resource = SafetyInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}