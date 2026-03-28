<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Modules\Farms\Models\FarmCarbonRecord;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\CropCycle;
use Filament\Facades\Filament;

class FarmCarbonResource extends Resource
{
    protected static ?string $model = FarmCarbonRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';
    protected static ?string $navigationLabel = 'Carbon / ESG Records';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        $companyId = Filament::getTenant()?->id;

        return $schema->components([
            Section::make('Record Period')->schema([
                Grid::make(2)->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', $companyId)->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('record_period')
                        ->label('Period Label')
                        ->placeholder('e.g. 2025-Q1, 2025 Season A')
                        ->required()
                        ->maxLength(100),
                    DatePicker::make('period_start')->required(),
                    DatePicker::make('period_end')->required(),
                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->options(fn () => CropCycle::where('company_id', $companyId)->pluck('cycle_name', 'id'))
                        ->nullable()
                        ->searchable(),
                    TextInput::make('farm_area_ha')
                        ->numeric()
                        ->suffix('ha')
                        ->label('Farm Area (ha)'),
                    TextInput::make('total_production_kg')
                        ->numeric()
                        ->suffix('kg')
                        ->label('Total Production (kg)'),
                ]),
            ]),
            Section::make('Emission Sources (tCO2e)')->schema([
                Grid::make(2)->schema([
                    TextInput::make('fertilizer_emissions_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Fertilizer Emissions'),
                    TextInput::make('fuel_emissions_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Fuel Emissions'),
                    TextInput::make('livestock_emissions_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Livestock Emissions'),
                    TextInput::make('electricity_emissions_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Electricity Emissions'),
                    TextInput::make('other_emissions_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Other Emissions'),
                ]),
            ]),
            Section::make('Sequestration (tCO2e)')->schema([
                Grid::make(2)->schema([
                    TextInput::make('soil_sequestration_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Soil Sequestration'),
                    TextInput::make('tree_sequestration_tco2e')
                        ->numeric()
                        ->default(0)
                        ->label('Tree Sequestration'),
                ]),
            ]),
            Section::make('Water Use')->schema([
                Grid::make(2)->schema([
                    TextInput::make('water_used_m3')
                        ->numeric()
                        ->default(0)
                        ->suffix('m³')
                        ->label('Water Used (m³)'),
                ]),
            ]),
            Section::make('Notes')->schema([
                Textarea::make('methodology_notes')
                    ->label('Methodology Notes')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('record_period')->sortable()->searchable(),
                TextColumn::make('total_emissions_tco2e')
                    ->label('Total Emissions (tCO2e)')
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('net_emissions_tco2e')
                    ->label('Net Emissions (tCO2e)')
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('emissions_per_ha')
                    ->label('Per Ha (tCO2e/ha)')
                    ->numeric(decimalPlaces: 4)
                    ->placeholder('—'),
                TextColumn::make('emissions_per_kg')
                    ->label('Per kg (tCO2e/kg)')
                    ->numeric(decimalPlaces: 6)
                    ->placeholder('—'),
                TextColumn::make('water_per_tonne_produce')
                    ->label('Water / tonne (m³)')
                    ->numeric(decimalPlaces: 4)
                    ->placeholder('—'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\FarmCarbonResource\Pages\ListFarmCarbonRecords::route('/'),
            'create' => \Modules\Farms\Filament\Resources\FarmCarbonResource\Pages\CreateFarmCarbonRecord::route('/create'),
            'edit'   => \Modules\Farms\Filament\Resources\FarmCarbonResource\Pages\EditFarmCarbonRecord::route('/{record}/edit'),
        ];
    }
}