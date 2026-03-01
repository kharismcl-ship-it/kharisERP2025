<?php

namespace Modules\Farms\Filament\Resources\FarmTaskResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmTaskResource;

class EditFarmTask extends EditRecord
{
    protected static string $resource = FarmTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}