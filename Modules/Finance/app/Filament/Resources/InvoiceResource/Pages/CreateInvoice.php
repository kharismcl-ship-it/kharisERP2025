<?php

namespace Modules\Finance\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
