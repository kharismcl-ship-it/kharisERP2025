<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource;

class EditMyFarmRequest extends EditRecord
{
    protected static string $resource = MyFarmRequestResource::class;

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
