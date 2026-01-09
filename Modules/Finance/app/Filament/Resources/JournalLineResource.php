<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\JournalLineResource\Pages;
use Modules\Finance\Models\JournalLine;

class JournalLineResource extends Resource
{
    protected static ?string $model = JournalLine::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('journal_entry_id')
                    ->relationship('journalEntry', 'reference')
                    ->required(),
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Forms\Components\TextInput::make('debit')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('credit')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('journalEntry.reference')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalLines::route('/'),
            'create' => Pages\CreateJournalLine::route('/create'),
            'edit' => Pages\EditJournalLine::route('/{record}/edit'),
        ];
    }
}
