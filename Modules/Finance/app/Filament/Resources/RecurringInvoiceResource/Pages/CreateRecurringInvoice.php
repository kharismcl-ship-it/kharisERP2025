<?php

namespace Modules\Finance\Filament\Resources\RecurringInvoiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\RecurringInvoiceResource;

class CreateRecurringInvoice extends CreateRecord
{
    protected static string $resource = RecurringInvoiceResource::class;
}
