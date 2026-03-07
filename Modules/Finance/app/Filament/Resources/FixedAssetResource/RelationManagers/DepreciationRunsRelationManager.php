<?php

namespace Modules\Finance\Filament\Resources\FixedAssetResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepreciationRunsRelationManager extends RelationManager
{
    protected static string $relationship = 'depreciationRuns';

    protected static ?string $title = 'Depreciation History';

    public function form(Schema $schema): Schema
    {
        // Records are created via ViewFixedAsset "Record Depreciation" action, not directly here.
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_end_date')
                    ->label('Period End')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('GHS')
                    ->weight('bold'),

                TextColumn::make('accumulated_before')
                    ->label('Accum. Before')
                    ->money('GHS'),

                TextColumn::make('accumulated_after')
                    ->label('Accum. After')
                    ->money('GHS'),

                TextColumn::make('journalEntry.reference')
                    ->label('Journal Ref.')
                    ->placeholder('—'),

                TextColumn::make('postedBy.name')
                    ->label('Posted By')
                    ->placeholder('—'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Posted At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('period_end_date', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}