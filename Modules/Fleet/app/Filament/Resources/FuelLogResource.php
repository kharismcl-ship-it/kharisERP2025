<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
            Section::make()->schema([
                Grid::make(2)->schema([
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('driver_id')
                        ->label('Driver')
                        ->relationship('driver', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(3)->schema([
                    DatePicker::make('fill_date')->required(),
                    TextInput::make('litres')->required()->numeric()->step(0.001)->suffix('L'),
                    TextInput::make('price_per_litre')->required()->numeric()->prefix('GHS')->step(0.0001),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('total_cost')->numeric()->prefix('GHS')->step(0.01)->label('Total Cost'),
                    TextInput::make('mileage_at_fill')->label('Mileage at Fill (km)')->numeric()->step(0.01),
                    TextInput::make('fuel_station')->label('Fuel Station')->maxLength(255),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
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
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fill_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'edit'   => Pages\EditFuelLog::route('/{record}/edit'),
        ];
    }
}
