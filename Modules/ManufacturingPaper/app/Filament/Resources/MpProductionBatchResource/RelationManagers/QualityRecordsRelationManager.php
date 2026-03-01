<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
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

class QualityRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'qualityRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Test Details')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('test_date')->required()->default(now()),
                    TextInput::make('tested_by')->maxLength(255),
                ]),
            ]),

            Section::make('Mechanical Properties')->schema([
                Grid::make(3)->schema([
                    TextInput::make('tensile_cd')->label('Tensile CD (kN/m)')->numeric()->step(0.001),
                    TextInput::make('tensile_md')->label('Tensile MD (kN/m)')->numeric()->step(0.001),
                    TextInput::make('burst_strength')->label('Burst Strength (kPa)')->numeric()->step(0.001),
                ]),
            ]),

            Section::make('Physical Properties')->schema([
                Grid::make(4)->schema([
                    TextInput::make('moisture_percent')->label('Moisture %')->numeric()->step(0.01),
                    TextInput::make('brightness')->label('Brightness %')->numeric()->step(0.01),
                    TextInput::make('opacity')->label('Opacity %')->numeric()->step(0.01),
                    TextInput::make('roughness')->label('Roughness')->numeric()->step(0.01),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('basis_weight')->label('Basis Weight (g/m²)')->numeric()->step(0.01),
                    Toggle::make('passed')->label('Quality Passed')->default(false)->inline(false),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('test_date')->date()->sortable(),
                TextColumn::make('tested_by')->label('Tested By'),
                TextColumn::make('basis_weight')->label('GSM')->numeric(decimalPlaces: 2),
                TextColumn::make('moisture_percent')->label('Moisture %')->numeric(decimalPlaces: 2),
                TextColumn::make('brightness')->label('Brightness %')->numeric(decimalPlaces: 2),
                TextColumn::make('burst_strength')->label('Burst (kPa)')->numeric(decimalPlaces: 3),
                IconColumn::make('passed')->label('Passed')->boolean(),
            ])
            ->defaultSort('test_date', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
