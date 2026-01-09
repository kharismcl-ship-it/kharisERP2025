<?php

namespace Modules\Finance\Filament\Resources\JournalLineResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\JournalLineResource;

class ListJournalLines extends ListRecords
{
    protected static string $resource = JournalLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
