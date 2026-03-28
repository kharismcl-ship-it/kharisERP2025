<?php

namespace Modules\HR\Filament\Resources\SurveyResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\SurveyResource;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}