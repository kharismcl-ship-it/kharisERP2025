<?php

namespace Modules\Farms\Filament\Resources\FarmBundleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmBundleResource;

class ListFarmBundles extends ListRecords
{
    protected static string $resource = FarmBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
