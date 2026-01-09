<?php

namespace Modules\HR\Filament\Resources\JobPositionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\JobPositionResource;

class EditJobPosition extends EditRecord
{
    protected static string $resource = JobPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
