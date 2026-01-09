<?php

namespace Modules\Hostels\Filament\Resources\HostelBillingRuleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelBillingRuleResource;

class ListHostelBillingRules extends ListRecords
{
    protected static string $resource = HostelBillingRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Billing Rule'),
        ];
    }
}
