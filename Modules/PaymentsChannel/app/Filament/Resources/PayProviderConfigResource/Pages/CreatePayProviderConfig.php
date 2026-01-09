<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource;

class CreatePayProviderConfig extends CreateRecord
{
    protected static string $resource = PayProviderConfigResource::class;
}
