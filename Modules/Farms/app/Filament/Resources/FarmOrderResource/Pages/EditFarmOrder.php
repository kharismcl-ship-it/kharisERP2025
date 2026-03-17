<?php

namespace Modules\Farms\Filament\Resources\FarmOrderResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmOrderResource;

class EditFarmOrder extends EditRecord
{
    protected static string $resource = FarmOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
