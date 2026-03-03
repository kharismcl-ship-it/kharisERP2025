<?php

namespace Modules\ClientService\Filament\Resources\CsAttendanceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ClientService\Filament\Resources\CsAttendanceResource;

class EditCsAttendance extends EditRecord
{
    protected static string $resource = CsAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
