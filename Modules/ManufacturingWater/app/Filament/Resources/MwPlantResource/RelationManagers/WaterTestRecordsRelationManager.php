<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;

class WaterTestRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'waterTestRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                DatePicker::make('test_date')->required()->default(now()),
                Select::make('test_type')
                    ->options(array_combine(MwWaterTestRecord::TEST_TYPES, array_map('ucfirst', MwWaterTestRecord::TEST_TYPES)))
                    ->required(),
                TextInput::make('tested_by')->maxLength(255),
            ]),
            Section::make('Quality Parameters')->schema([
                Grid::make(4)->schema([
                    TextInput::make('ph')->label('pH')->numeric()->step(0.01),
                    TextInput::make('turbidity_ntu')->label('Turbidity (NTU)')->numeric()->step(0.001),
                    TextInput::make('tds_ppm')->label('TDS (ppm)')->numeric()->step(0.01),
                    TextInput::make('temperature_c')->label('Temp (°C)')->numeric()->step(0.01),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('coliform_count')->label('Coliform (CFU/100ml)')->numeric()->step(0.01),
                    TextInput::make('chlorine_residual')->label('Chlorine Residual (mg/L)')->numeric()->step(0.001),
                    TextInput::make('dissolved_oxygen')->label('DO (mg/L)')->numeric()->step(0.001),
                ]),
                Grid::make(2)->schema([
                    Toggle::make('passed')->label('Passed')->default(false)->inline(false),
                    Textarea::make('notes')->rows(2),
                ]),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('test_date')->date()->sortable(),
                TextColumn::make('test_type')->label('Type')->badge(),
                TextColumn::make('ph')->label('pH')->numeric(decimalPlaces: 2),
                TextColumn::make('turbidity_ntu')->label('NTU')->numeric(decimalPlaces: 3),
                TextColumn::make('tds_ppm')->label('TDS')->numeric(decimalPlaces: 2),
                TextColumn::make('chlorine_residual')->label('Cl₂ (mg/L)')->numeric(decimalPlaces: 3),
                IconColumn::make('passed')->label('Passed')->boolean(),
                TextColumn::make('tested_by')->label('By'),
            ])
            ->defaultSort('test_date', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}