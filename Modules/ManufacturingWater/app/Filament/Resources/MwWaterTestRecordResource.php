<?php

namespace Modules\ManufacturingWater\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ManufacturingWater\Filament\Resources\MwWaterTestRecordResource\Pages;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;

class MwWaterTestRecordResource extends Resource
{
    protected static ?string $model = MwWaterTestRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-eye-dropper';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Test Info')->schema([
                Grid::make(3)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    DatePicker::make('test_date')->required()->default(now()),
                    Select::make('test_type')
                        ->label('Test Type')
                        ->options(array_combine(MwWaterTestRecord::TEST_TYPES, array_map('ucfirst', MwWaterTestRecord::TEST_TYPES)))
                        ->required(),
                ]),
                TextInput::make('tested_by')->maxLength(255),
            ]),

            Section::make('Parameters')->schema([
                Grid::make(4)->schema([
                    TextInput::make('ph')->label('pH')->numeric()->step(0.01),
                    TextInput::make('turbidity_ntu')->label('Turbidity (NTU)')->numeric()->step(0.001),
                    TextInput::make('tds_ppm')->label('TDS (ppm)')->numeric()->step(0.01),
                    TextInput::make('temperature_c')->label('Temp (°C)')->numeric()->step(0.01),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('coliform_count')->label('Coliform (CFU/100ml)')->numeric()->step(0.01),
                    TextInput::make('chlorine_residual')->label('Chlorine (mg/L)')->numeric()->step(0.001),
                    TextInput::make('dissolved_oxygen')->label('DO (mg/L)')->numeric()->step(0.001),
                ]),
            ]),

            Section::make('Result')->schema([
                Grid::make(2)->schema([
                    Toggle::make('passed')->label('Quality Passed')->default(false)->inline(false),
                    Textarea::make('notes')->rows(2),
                ]),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Test Info')->columns(3)->schema([
                TextEntry::make('plant.name')->label('Plant'),
                TextEntry::make('test_date')->date(),
                TextEntry::make('test_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'final'        => 'success',
                        'treated'      => 'info',
                        'raw'          => 'warning',
                        'distribution' => 'primary',
                        default        => 'gray',
                    }),
                TextEntry::make('tested_by')->placeholder('—'),
            ]),
            Section::make('Parameters')->columns(4)->schema([
                TextEntry::make('ph')->label('pH')->placeholder('—'),
                TextEntry::make('turbidity_ntu')->label('Turbidity (NTU)')->placeholder('—'),
                TextEntry::make('tds_ppm')->label('TDS (ppm)')->placeholder('—'),
                TextEntry::make('temperature_c')->label('Temp (°C)')->placeholder('—'),
                TextEntry::make('coliform_count')->label('Coliform (CFU/100ml)')->placeholder('—'),
                TextEntry::make('chlorine_residual')->label('Chlorine (mg/L)')->placeholder('—'),
                TextEntry::make('dissolved_oxygen')->label('DO (mg/L)')->placeholder('—'),
            ]),
            Section::make('Result')->columns(2)->schema([
                IconEntry::make('passed')->label('Quality Passed')->boolean(),
                TextEntry::make('notes')->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plant.name')->label('Plant')->searchable()->sortable(),
                TextColumn::make('test_date')->date()->sortable(),
                TextColumn::make('test_type')->label('Type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'final'        => 'success',
                        'treated'      => 'info',
                        'raw'          => 'warning',
                        'distribution' => 'primary',
                        default        => 'gray',
                    }),
                TextColumn::make('ph')->label('pH')->numeric(decimalPlaces: 2),
                TextColumn::make('turbidity_ntu')->label('NTU')->numeric(decimalPlaces: 3),
                TextColumn::make('tds_ppm')->label('TDS')->numeric(decimalPlaces: 2),
                TextColumn::make('chlorine_residual')->label('Cl₂')->numeric(decimalPlaces: 3),
                IconColumn::make('passed')->label('Passed')->boolean(),
                TextColumn::make('tested_by')->label('By'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
                Tables\Filters\SelectFilter::make('test_type')
                    ->options(array_combine(MwWaterTestRecord::TEST_TYPES, array_map('ucfirst', MwWaterTestRecord::TEST_TYPES))),
                Tables\Filters\TernaryFilter::make('passed'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index'  => Pages\ListMwWaterTestRecords::route('/'),
            'create' => Pages\CreateMwWaterTestRecord::route('/create'),
            'view'   => Pages\ViewMwWaterTestRecord::route('/{record}'),
            'edit'   => Pages\EditMwWaterTestRecord::route('/{record}/edit'),
        ];
    }
}
