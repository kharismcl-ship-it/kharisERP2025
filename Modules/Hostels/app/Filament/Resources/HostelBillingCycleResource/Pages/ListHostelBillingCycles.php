<?php

namespace Modules\Hostels\Filament\Resources\HostelBillingCycleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelBillingCycleResource;

class ListHostelBillingCycles extends ListRecords
{
    protected static string $resource = HostelBillingCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Billing Cycle'),
        ];
    }
}
