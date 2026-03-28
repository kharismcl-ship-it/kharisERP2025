<?php

namespace Modules\Farms\Filament\Resources\FarmAgronomistResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmAgronomistResource;

class EditFarmAgronomist extends EditRecord
{
    protected static string $resource = FarmAgronomistResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}