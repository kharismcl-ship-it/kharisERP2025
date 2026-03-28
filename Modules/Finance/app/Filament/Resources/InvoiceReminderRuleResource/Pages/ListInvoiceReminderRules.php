<?php

namespace Modules\Finance\Filament\Resources\InvoiceReminderRuleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\InvoiceReminderRuleResource;

class ListInvoiceReminderRules extends ListRecords
{
    protected static string $resource = InvoiceReminderRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}