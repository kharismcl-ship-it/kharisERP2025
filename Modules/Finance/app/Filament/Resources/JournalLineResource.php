<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\JournalLineResource\Pages;
use Modules\Finance\Models\JournalLine;

class JournalLineResource extends Resource
{
    protected static ?string $model = JournalLine::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 42;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Journal Line')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('journal_entry_id')
                            ->relationship('journalEntry', 'reference')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('account_id')
                            ->relationship('account', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('debit')
                            ->required()
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                        Forms\Components\TextInput::make('credit')
                            ->required()
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('journalEntry.reference')
                    ->label('Journal Entry')
                    ->sortable(),
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJournalLines::route('/'),
            'create' => Pages\CreateJournalLine::route('/create'),
            'view'   => Pages\ViewJournalLine::route('/{record}'),
            'edit'   => Pages\EditJournalLine::route('/{record}/edit'),
        ];
    }
}