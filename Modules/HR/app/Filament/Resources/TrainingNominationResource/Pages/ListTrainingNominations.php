<?php

namespace Modules\HR\Filament\Resources\TrainingNominationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\TrainingNominationResource;

class ListTrainingNominations extends ListRecords
{
    protected static string $resource = TrainingNominationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
