<?php

namespace Modules\Farms\Filament\Resources\FarmPostHarvestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmPostHarvestResource;

class EditFarmPostHarvestRecord extends EditRecord
{
    protected static string $resource = FarmPostHarvestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}