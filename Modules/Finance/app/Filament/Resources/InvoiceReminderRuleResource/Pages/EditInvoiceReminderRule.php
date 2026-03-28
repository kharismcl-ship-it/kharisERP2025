<?php

namespace Modules\Finance\Filament\Resources\InvoiceReminderRuleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\InvoiceReminderRuleResource;

class EditInvoiceReminderRule extends EditRecord
{
    protected static string $resource = InvoiceReminderRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}