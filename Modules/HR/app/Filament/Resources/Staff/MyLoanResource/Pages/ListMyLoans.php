<?php

namespace Modules\HR\Filament\Resources\Staff\MyLoanResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyLoanResource;

class ListMyLoans extends ListRecords
{
    protected static string $resource = MyLoanResource::class;
}
