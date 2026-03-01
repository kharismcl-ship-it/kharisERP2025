<?php

namespace Modules\Finance\Filament\Resources\JournalEntryResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JournalLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'journalLines';

    protected static ?string $title = 'Journal Lines (Debit / Credit)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required()
                    ->columnSpanFull()
                    ->searchable()
                    ->preload(),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.name')->label('Account')->sortable(),
                Tables\Columns\TextColumn::make('debit')->money('GHS'),
                Tables\Columns\TextColumn::make('credit')->money('GHS'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
