<?php

namespace Modules\HR\Filament\Resources\SurveyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\SurveyResource;

class ListSurveys extends ListRecords
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}