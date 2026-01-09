<?php

namespace Modules\Hostels\Filament\Resources\HostelUtilityChargeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelUtilityChargeResource;

class ListHostelUtilityCharges extends ListRecords
{
    protected static string $resource = HostelUtilityChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Utility Charge'),
        ];
    }
}
