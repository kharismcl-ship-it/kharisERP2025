<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayIntentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PaymentsChannel\Filament\Resources\PayIntentResource;

class CreatePayIntent extends CreateRecord
{
    protected static string $resource = PayIntentResource::class;
}
