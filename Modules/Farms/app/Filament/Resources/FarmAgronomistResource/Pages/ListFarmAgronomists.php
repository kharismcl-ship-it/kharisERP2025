<?php

namespace Modules\Farms\Filament\Resources\FarmAgronomistResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmAgronomistResource;

class ListFarmAgronomists extends ListRecords
{
    protected static string $resource = FarmAgronomistResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}