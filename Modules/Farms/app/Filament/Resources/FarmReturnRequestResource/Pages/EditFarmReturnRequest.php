<?php

namespace Modules\Farms\Filament\Resources\FarmReturnRequestResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmReturnRequestResource;

class EditFarmReturnRequest extends EditRecord
{
    protected static string $resource = FarmReturnRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
