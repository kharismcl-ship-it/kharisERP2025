<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource;

class ViewMyLeaveRequest extends ViewRecord
{
    protected static string $resource = MyLeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => static::getResource()::canEdit($this->record)),
            DeleteAction::make()
                ->visible(fn () => static::getResource()::canDelete($this->record)),
        ];
    }
}
