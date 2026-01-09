<?php

namespace Modules\Finance\Filament\Resources\InvoiceLineResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\InvoiceLineResource;

class CreateInvoiceLine extends CreateRecord
{
    protected static string $resource = InvoiceLineResource::class;
}
