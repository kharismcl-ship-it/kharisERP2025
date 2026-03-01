<?php

namespace Modules\Farms\Filament\Resources\LivestockHealthRecordResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource;

class ListLivestockHealthRecords extends ListRecords
{
    protected static string $resource = LivestockHealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}