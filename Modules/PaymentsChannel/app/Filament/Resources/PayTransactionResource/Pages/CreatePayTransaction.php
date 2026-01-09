<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayTransactionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PaymentsChannel\Filament\Resources\PayTransactionResource;

class CreatePayTransaction extends CreateRecord
{
    protected static string $resource = PayTransactionResource::class;
}
