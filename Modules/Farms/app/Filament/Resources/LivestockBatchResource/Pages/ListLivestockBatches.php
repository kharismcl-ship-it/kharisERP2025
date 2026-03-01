<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\LivestockBatchResource;

class ListLivestockBatches extends ListRecords
{
    protected static string $resource = LivestockBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}