<?php

namespace Modules\Construction\Filament\Resources\ContractorRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ContractorRequestResource;

class ListContractorRequests extends ListRecords
{
    protected static string $resource = ContractorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
