<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayTransactionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\PaymentsChannel\Filament\Resources\PayTransactionResource;

class ListPayTransactions extends ListRecords
{
    protected static string $resource = PayTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
