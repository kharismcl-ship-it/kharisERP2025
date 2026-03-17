<?php

namespace Modules\HR\Filament\Resources\OffboardingChecklistResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\OffboardingChecklistResource;

class EditOffboardingChecklist extends EditRecord
{
    protected static string $resource = OffboardingChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
