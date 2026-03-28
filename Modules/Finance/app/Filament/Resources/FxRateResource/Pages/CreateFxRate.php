<?php

namespace Modules\Finance\Filament\Resources\FxRateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\FxRateResource;

class CreateFxRate extends CreateRecord
{
    protected static string $resource = FxRateResource::class;
}