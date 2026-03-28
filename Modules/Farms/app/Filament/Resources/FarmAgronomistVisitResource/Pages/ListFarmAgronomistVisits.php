<?php

namespace Modules\Farms\Filament\Resources\FarmAgronomistVisitResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmAgronomistVisitResource;

class ListFarmAgronomistVisits extends ListRecords
{
    protected static string $resource = FarmAgronomistVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}