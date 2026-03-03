<?php

namespace Modules\ITSupport\Filament\Resources\ItTrainingSessionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ITSupport\Filament\Resources\ItTrainingSessionResource;

class ListItTrainingSessions extends ListRecords
{
    protected static string $resource = ItTrainingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
