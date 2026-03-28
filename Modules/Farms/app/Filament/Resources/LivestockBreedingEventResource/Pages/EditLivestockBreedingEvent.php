<?php

namespace Modules\Farms\Filament\Resources\LivestockBreedingEventResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\LivestockBreedingEventResource;

class EditLivestockBreedingEvent extends EditRecord
{
    protected static string $resource = LivestockBreedingEventResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}