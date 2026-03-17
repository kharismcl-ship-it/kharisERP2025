<?php

namespace Modules\HR\Filament\Resources\OffboardingChecklistResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\OffboardingChecklistResource;

class ListOffboardingChecklists extends ListRecords
{
    protected static string $resource = OffboardingChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
