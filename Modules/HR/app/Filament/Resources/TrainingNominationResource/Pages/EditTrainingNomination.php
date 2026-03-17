<?php

namespace Modules\HR\Filament\Resources\TrainingNominationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\TrainingNominationResource;

class EditTrainingNomination extends EditRecord
{
    protected static string $resource = TrainingNominationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
