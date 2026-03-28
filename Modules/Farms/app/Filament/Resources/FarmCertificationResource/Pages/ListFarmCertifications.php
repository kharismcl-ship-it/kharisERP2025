<?php

namespace Modules\Farms\Filament\Resources\FarmCertificationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmCertificationResource;

class ListFarmCertifications extends ListRecords
{
    protected static string $resource = FarmCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
