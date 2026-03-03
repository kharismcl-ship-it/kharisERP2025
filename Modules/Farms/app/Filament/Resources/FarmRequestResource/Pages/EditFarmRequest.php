<?php

namespace Modules\Farms\Filament\Resources\FarmRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmRequestResource;

class EditFarmRequest extends EditRecord
{
    protected static string $resource = FarmRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
