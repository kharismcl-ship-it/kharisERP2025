<?php

namespace Modules\Farms\Filament\Resources\HarvestRecordResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\HarvestRecordResource;

class ListHarvestRecords extends ListRecords
{
    protected static string $resource = HarvestRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
