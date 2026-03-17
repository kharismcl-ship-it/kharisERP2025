<?php

namespace Modules\HR\Filament\Resources\OffboardingChecklistResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\OffboardingChecklistResource;

class ViewOffboardingChecklist extends ViewRecord
{
    protected static string $resource = OffboardingChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
