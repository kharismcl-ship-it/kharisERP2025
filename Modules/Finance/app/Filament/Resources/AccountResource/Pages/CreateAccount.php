<?php

namespace Modules\Finance\Filament\Resources\AccountResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\AccountResource;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;
}
