<?php

namespace Modules\Hostels\Filament\Resources\PricingPolicyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\PricingPolicyResource;

class ListPricingPolicies extends ListRecords
{
    protected static string $resource = PricingPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
