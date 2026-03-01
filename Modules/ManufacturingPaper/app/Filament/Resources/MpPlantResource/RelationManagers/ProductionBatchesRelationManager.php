<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingPaper\Models\MpProductionBatch;

class ProductionBatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'productionBatches';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_number')->label('Batch No.')->searchable()->sortable(),
                TextColumn::make('paperGrade.name')->label('Grade'),
                TextColumn::make('productionLine.name')->label('Line'),
                TextColumn::make('quantity_planned')->label('Planned')->numeric(decimalPlaces: 2),
                TextColumn::make('quantity_produced')->label('Produced')->numeric(decimalPlaces: 2),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'planned'     => 'info',
                        'on_hold'     => 'gray',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('start_time')->dateTime()->label('Started'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
