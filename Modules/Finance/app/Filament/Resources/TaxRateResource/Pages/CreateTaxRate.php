<?php

namespace Modules\Finance\Filament\Resources\TaxRateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\TaxRateResource;

class CreateTaxRate extends CreateRecord
{
    protected static string $resource = TaxRateResource::class;
}