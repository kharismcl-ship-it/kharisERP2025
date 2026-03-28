<?php

namespace Modules\Finance\Filament\Resources\PaymentBatchResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\PaymentBatchResource;

class EditPaymentBatch extends EditRecord
{
    protected static string $resource = PaymentBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}