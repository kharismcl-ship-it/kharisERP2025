<?php

namespace Modules\Sales\Filament\Resources\ContactResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\ContactResource;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
