<?php

namespace Modules\ITSupport\Filament\Resources\ItTrainingSessionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ITSupport\Filament\Resources\ItTrainingSessionResource;

class EditItTrainingSession extends EditRecord
{
    protected static string $resource = ItTrainingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
