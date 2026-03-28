<?php

namespace Modules\Finance\Filament\Resources\PaymentAllocationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\PaymentAllocationResource;

class CreatePaymentAllocation extends CreateRecord
{
    protected static string $resource = PaymentAllocationResource::class;
}