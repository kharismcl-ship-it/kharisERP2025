<?php

namespace Modules\HR\Filament\Resources\GrievanceCaseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\GrievanceCaseResource;

class EditGrievanceCase extends EditRecord
{
    protected static string $resource = GrievanceCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}