<?php

namespace Modules\Finance\Filament\Resources\ChequeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\ChequeResource;

class CreateCheque extends CreateRecord
{
    protected static string $resource = ChequeResource::class;
}