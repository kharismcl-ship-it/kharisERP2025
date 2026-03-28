<?php

namespace Modules\Finance\Filament\Resources\PaymentBatchResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\PaymentBatchResource;

class CreatePaymentBatch extends CreateRecord
{
    protected static string $resource = PaymentBatchResource::class;
}