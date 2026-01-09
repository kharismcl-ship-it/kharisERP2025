<?php

namespace Modules\HR\Filament\Resources\LeaveTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\LeaveTypeResource;

class EditLeaveType extends EditRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
