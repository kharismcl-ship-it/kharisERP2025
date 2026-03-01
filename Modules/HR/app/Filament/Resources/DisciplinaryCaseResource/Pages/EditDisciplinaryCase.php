<?php

namespace Modules\HR\Filament\Resources\DisciplinaryCaseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\DisciplinaryCaseResource;

class EditDisciplinaryCase extends EditRecord
{
    protected static string $resource = DisciplinaryCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}