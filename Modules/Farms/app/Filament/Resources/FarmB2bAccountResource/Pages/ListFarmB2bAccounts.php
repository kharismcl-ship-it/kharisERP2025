<?php

namespace Modules\Farms\Filament\Resources\FarmB2bAccountResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmB2bAccountResource;

class ListFarmB2bAccounts extends ListRecords
{
    protected static string $resource = FarmB2bAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
