<?php

namespace Modules\Finance\Filament\Resources\CreditNoteResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\CreditNoteResource;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;
}