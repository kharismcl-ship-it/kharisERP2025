<?php

namespace Modules\Finance\Filament\Resources\AccountingPeriodResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\AccountingPeriodResource;

class ListAccountingPeriods extends ListRecords
{
    protected static string $resource = AccountingPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
