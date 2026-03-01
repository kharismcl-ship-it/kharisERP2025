<?php

namespace Modules\Sales\Filament\Resources\SalesOrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\SalesOrderResource;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;
}
