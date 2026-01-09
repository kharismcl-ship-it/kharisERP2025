<?php

namespace Modules\Finance\Filament\Resources\InvoiceLineResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\InvoiceLineResource;

class EditInvoiceLine extends EditRecord
{
    protected static string $resource = InvoiceLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
