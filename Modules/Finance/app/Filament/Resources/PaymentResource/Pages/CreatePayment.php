<?php

namespace Modules\Finance\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\PaymentResource;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
