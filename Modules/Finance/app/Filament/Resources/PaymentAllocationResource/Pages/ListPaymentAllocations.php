<?php

namespace Modules\Finance\Filament\Resources\PaymentAllocationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\PaymentAllocationResource;

class ListPaymentAllocations extends ListRecords
{
    protected static string $resource = PaymentAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}