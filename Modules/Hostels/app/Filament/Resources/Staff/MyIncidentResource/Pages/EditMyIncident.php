<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource;

class EditMyIncident extends EditRecord
{
    protected static string $resource = MyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
