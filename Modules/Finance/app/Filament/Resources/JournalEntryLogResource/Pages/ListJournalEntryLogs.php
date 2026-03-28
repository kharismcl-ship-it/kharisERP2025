<?php

namespace Modules\Finance\Filament\Resources\JournalEntryLogResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\JournalEntryLogResource;

class ListJournalEntryLogs extends ListRecords
{
    protected static string $resource = JournalEntryLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}