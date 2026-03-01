<?php

namespace Modules\Finance\Filament\Resources\JournalLineResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\JournalLineResource;

class ViewJournalLine extends ViewRecord
{
    protected static string $resource = JournalLineResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Journal Line')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('journalEntry.reference')->label('Journal Entry')->weight('bold'),
                        TextEntry::make('account.name')->label('Account'),
                        TextEntry::make('debit')->money('GHS')->label('Debit'),
                        TextEntry::make('credit')->money('GHS')->label('Credit'),
                    ]),
            ]);
    }
}
