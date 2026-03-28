<?php

namespace Modules\Finance\Filament\Resources\PaymentBatchResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\PaymentBatchResource;

class ListPaymentBatches extends ListRecords
{
    protected static string $resource = PaymentBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}