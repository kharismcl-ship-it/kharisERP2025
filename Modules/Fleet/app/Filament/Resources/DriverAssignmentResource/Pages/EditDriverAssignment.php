<?php

namespace Modules\Fleet\Filament\Resources\DriverAssignmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Fleet\Filament\Resources\DriverAssignmentResource;

class EditDriverAssignment extends EditRecord
{
    protected static string $resource = DriverAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
