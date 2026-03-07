<?php

namespace Modules\Finance\Filament\Resources\FixedAssetResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransfersRelationManager extends RelationManager
{
    protected static string $relationship = 'transfers';

    protected static ?string $title = 'Transfer History';

    public function form(Schema $schema): Schema
    {
        // Transfers are created via ViewFixedAsset "Transfer Location" action, not here directly.
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transfer_date')
                    ->label('Transfer Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('from_location')
                    ->label('From Location')
                    ->placeholder('—'),

                TextColumn::make('to_location')
                    ->label('To Location')
                    ->weight('bold'),

                TextColumn::make('fromCustodian.name')
                    ->label('From Custodian')
                    ->placeholder('—'),

                TextColumn::make('toCustodian.name')
                    ->label('To Custodian')
                    ->placeholder('—'),

                TextColumn::make('transferredBy.name')
                    ->label('Transferred By')
                    ->placeholder('—'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transfer_date', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}