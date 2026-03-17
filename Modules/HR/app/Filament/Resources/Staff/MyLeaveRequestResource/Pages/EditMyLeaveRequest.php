<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource;

class EditMyLeaveRequest extends EditRecord
{
    protected static string $resource = MyLeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
