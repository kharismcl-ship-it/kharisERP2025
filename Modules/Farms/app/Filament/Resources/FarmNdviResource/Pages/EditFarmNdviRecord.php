<?php

namespace Modules\Farms\Filament\Resources\FarmNdviResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmNdviResource;

class EditFarmNdviRecord extends EditRecord
{
    protected static string $resource = FarmNdviResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}