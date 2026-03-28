<?php

namespace Modules\Farms\Filament\Resources\FarmProduceLotResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmProduceLotResource;

class ListFarmProduceLots extends ListRecords
{
    protected static string $resource = FarmProduceLotResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}