<?php

namespace Modules\Hostels\Filament\Resources\BookingCancellationPolicyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\BookingCancellationPolicyResource;

class ListBookingCancellationPolicies extends ListRecords
{
    protected static string $resource = BookingCancellationPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
