<?php

namespace Modules\Finance\Filament\Resources\AdvancePaymentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\AdvancePaymentResource;

class ListAdvancePayments extends ListRecords
{
    protected static string $resource = AdvancePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}