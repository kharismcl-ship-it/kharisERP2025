<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class WeightRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'weightRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('record_date')->required(),
            TextInput::make('sample_size')->numeric()->minValue(1)->default(1),
            TextInput::make('avg_weight_kg')->label('Avg Weight (kg)')->required()->numeric()->step(0.001),
            TextInput::make('min_weight_kg')->label('Min Weight (kg)')->numeric()->step(0.001),
            TextInput::make('max_weight_kg')->label('Max Weight (kg)')->numeric()->step(0.001),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_date')->date('d M Y')->sortable(),
                TextColumn::make('sample_size')->label('Sample'),
                TextColumn::make('avg_weight_kg')->label('Avg (kg)')->numeric(decimalPlaces: 2),
                TextColumn::make('min_weight_kg')->label('Min (kg)')->numeric(decimalPlaces: 2)->placeholder('—'),
                TextColumn::make('max_weight_kg')->label('Max (kg)')->numeric(decimalPlaces: 2)->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('record_date', 'desc');
    }
}
