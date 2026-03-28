<?php

namespace Modules\Farms\Filament\Resources\FarmCooperativeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmCooperativeResource;

class EditFarmCooperative extends EditRecord
{
    protected static string $resource = FarmCooperativeResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
