<?php

namespace Modules\Finance\Filament\Resources\InvoiceLineResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\InvoiceLineResource;

class ListInvoiceLines extends ListRecords
{
    protected static string $resource = InvoiceLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
