<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayMethodResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PaymentsChannel\Filament\Resources\PayMethodResource;

class CreatePayMethod extends CreateRecord
{
    protected static string $resource = PayMethodResource::class;
}
