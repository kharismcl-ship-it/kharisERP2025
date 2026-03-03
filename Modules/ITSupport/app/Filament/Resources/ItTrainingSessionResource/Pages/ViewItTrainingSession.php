<?php

namespace Modules\ITSupport\Filament\Resources\ItTrainingSessionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ITSupport\Filament\Resources\ItTrainingSessionResource;

class ViewItTrainingSession extends ViewRecord
{
    protected static string $resource = ItTrainingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
