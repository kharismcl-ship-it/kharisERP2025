<?php

namespace Modules\Finance\Filament\Resources\JournalLineResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\JournalLineResource;

class EditJournalLine extends EditRecord
{
    protected static string $resource = JournalLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
