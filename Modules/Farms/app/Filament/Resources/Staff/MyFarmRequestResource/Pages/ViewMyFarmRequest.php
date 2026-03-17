<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource;

class ViewMyFarmRequest extends ViewRecord
{
    protected static string $resource = MyFarmRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
            DeleteAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
        ];
    }
}
