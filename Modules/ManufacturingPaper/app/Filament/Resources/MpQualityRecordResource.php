<?php

namespace Modules\ManufacturingPaper\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingPaper\Filament\Resources\MpQualityRecordResource\Pages;
use Modules\ManufacturingPaper\Models\MpQualityRecord;

class MpQualityRecordResource extends Resource
{
    protected static ?string $model = MpQualityRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Quality Records';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Test Details')->schema([
                Grid::make(3)->schema([
                    Select::make('production_batch_id')
                        ->label('Production Batch')
                        ->relationship('batch', 'batch_number')
                        ->searchable()
                        ->required(),
                    DatePicker::make('test_date')->required()->default(now()),
                    TextInput::make('tested_by')->maxLength(255),
                ]),
            ]),

            Section::make('Mechanical Properties')->schema([
                Grid::make(3)->schema([
                    TextInput::make('tensile_cd')->label('Tensile CD (N/m)')->numeric()->step(0.001),
                    TextInput::make('tensile_md')->label('Tensile MD (N/m)')->numeric()->step(0.001),
                    TextInput::make('burst_strength')->label('Burst Strength (kPa)')->numeric()->step(0.001),
                ]),
            ]),

            Section::make('Physical Properties')->schema([
                Grid::make(3)->schema([
                    TextInput::make('moisture_percent')->label('Moisture (%)')->numeric()->step(0.01),
                    TextInput::make('brightness')->label('Brightness (%)')->numeric()->step(0.01),
                    TextInput::make('opacity')->label('Opacity (%)')->numeric()->step(0.01),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('roughness')->label('Roughness (ml/min)')->numeric()->step(0.01),
                    TextInput::make('basis_weight')->label('Basis Weight (GSM)')->numeric()->step(0.01),
                    Toggle::make('passed')->label('Test Passed')->default(true)->inline(false),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch.batch_number')->label('Batch No.')->searchable()->sortable(),
                TextColumn::make('test_date')->date()->sortable(),
                TextColumn::make('tested_by')->label('Tester'),
                TextColumn::make('basis_weight')->label('GSM')->numeric(decimalPlaces: 2),
                TextColumn::make('moisture_percent')->label('Moisture %')->numeric(decimalPlaces: 2),
                TextColumn::make('brightness')->label('Brightness')->numeric(decimalPlaces: 2),
                TextColumn::make('burst_strength')->label('Burst Str.')->numeric(decimalPlaces: 3),
                IconColumn::make('passed')->label('Passed')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('production_batch_id')
                    ->label('Batch')
                    ->relationship('batch', 'batch_number'),
                Tables\Filters\TernaryFilter::make('passed')->label('Result'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('test_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMpQualityRecords::route('/'),
            'create' => Pages\CreateMpQualityRecord::route('/create'),
            'edit'   => Pages\EditMpQualityRecord::route('/{record}/edit'),
        ];
    }
}
