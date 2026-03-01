<?php

namespace Modules\Finance\Filament\Resources\JournalEntryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\JournalEntryResource;

class ViewJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Entry Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reference')->weight('bold'),
                        TextEntry::make('date')->date()->label('Entry Date'),
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    ]),

                Section::make('Audit')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->label('Created'),
                        TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                    ]),
            ]);
    }
}
