<?php

namespace Modules\Farms\Filament\Resources\FarmGrowerPaymentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmGrowerPaymentResource;

class ListFarmGrowerPayments extends ListRecords
{
    protected static string $resource = FarmGrowerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}