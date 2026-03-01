<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Fleet\Filament\Resources\FuelLogResource\Pages;
use Modules\Fleet\Models\FuelLog;

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Fill Details')
                ->description('Vehicle, driver, and fill event information')
                ->columns(2)
                ->schema([
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('driver_id')
                        ->label('Driver')
                        ->relationship('driver', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    DatePicker::make('fill_date')
                        ->label('Fill Date')
                        ->required()
                        ->displayFormat('d M Y'),
                    TextInput::make('fuel_station')
                        ->label('Fuel Station')
                        ->maxLength(255)
                        ->placeholder('e.g. Shell Accra Mall'),
                ]),

            Section::make('Cost & Consumption')
                ->description('Volume and pricing details — total cost is auto-calculated from litres × price')
                ->columns(3)
                ->schema([
                    TextInput::make('litres')
                        ->label('Volume (Litres)')
                        ->required()
                        ->numeric()
                        ->step(0.001)
                        ->suffix('L')
                        ->minValue(0.001),
                    TextInput::make('price_per_litre')
                        ->label('Price / Litre')
                        ->required()
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.0001)
                        ->minValue(0),
                    TextInput::make('total_cost')
                        ->label('Total Cost (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->helperText('Leave blank to auto-calculate'),
                    TextInput::make('mileage_at_fill')
                        ->label('Mileage at Fill')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('km')
                        ->placeholder('Odometer reading'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull()->placeholder('Any additional remarks...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.name')->label('Vehicle')->searchable()->sortable(),
                TextColumn::make('vehicle.plate')->label('Plate'),
                TextColumn::make('fill_date')->date()->sortable(),
                TextColumn::make('litres')->numeric(decimalPlaces: 2)->suffix(' L'),
                TextColumn::make('price_per_litre')->money('GHS')->label('Price/L'),
                TextColumn::make('total_cost')->money('GHS')->sortable(),
                TextColumn::make('mileage_at_fill')->label('Mileage')->numeric(decimalPlaces: 0),
                TextColumn::make('fuel_station')->label('Station'),
                TextColumn::make('driver.name')->label('Driver'),
            ])
            ->filters([
                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fill_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'view'   => Pages\ViewFuelLog::route('/{record}'),
            'edit'   => Pages\EditFuelLog::route('/{record}/edit'),
        ];
    }
}
