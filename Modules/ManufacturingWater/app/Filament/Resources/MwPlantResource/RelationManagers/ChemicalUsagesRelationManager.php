<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChemicalUsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'chemicalUsages';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('chemical_name')->required()->maxLength(255),
                Select::make('treatment_stage_id')
                    ->label('Treatment Stage')
                    ->relationship('treatmentStage', 'name')
                    ->searchable()
                    ->nullable(),
            ]),
            Grid::make(3)->schema([
                DatePicker::make('usage_date')->required()->default(now()),
                TextInput::make('quantity')->required()->numeric()->step(0.001),
                Select::make('unit')->options(['kg' => 'kg', 'litres' => 'Litres', 'ppm' => 'ppm', 'g' => 'g'])->default('kg'),
            ]),
            Grid::make(2)->schema([
                TextInput::make('unit_cost')->label('Unit Cost (GHS)')->numeric()->prefix('GHS')->step(0.0001),
                TextInput::make('purpose')->maxLength(255),
            ]),
            TextInput::make('batch_number')->label('Batch/Lot No.')->maxLength(50),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chemical_name')->label('Chemical')->searchable()->sortable(),
                TextColumn::make('treatmentStage.name')->label('Stage'),
                TextColumn::make('usage_date')->date()->sortable(),
                TextColumn::make('quantity')->numeric(decimalPlaces: 3),
                TextColumn::make('unit'),
                TextColumn::make('total_cost')->money('GHS')->sortable(),
                TextColumn::make('purpose'),
            ])
            ->defaultSort('usage_date', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
