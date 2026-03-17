<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource;

class EditMyVisitorLog extends EditRecord
{
    protected static string $resource = MyVisitorLogResource::class;

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
