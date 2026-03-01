<?php

namespace Modules\Sales\Filament\Resources\OpportunityResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\OpportunityResource;

class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
