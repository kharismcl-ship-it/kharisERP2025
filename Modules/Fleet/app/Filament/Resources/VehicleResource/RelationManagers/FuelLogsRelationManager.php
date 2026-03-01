<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use App\Models\User;

class FuelLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'fuelLogs';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('fill_date')->required(),
            TextInput::make('litres')->required()->numeric()->step(0.001),
            TextInput::make('price_per_litre')->required()->numeric()->prefix('GHS')->step(0.0001),
            TextInput::make('total_cost')->numeric()->prefix('GHS')->step(0.01)->label('Total Cost'),
            TextInput::make('mileage_at_fill')->label('Mileage at Fill')->numeric()->step(0.01),
            TextInput::make('fuel_station')->label('Fuel Station')->maxLength(255),
            Select::make('driver_id')
                ->label('Driver')
                ->relationship('driver', 'name')
                ->searchable()
                ->nullable(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fill_date')->date()->sortable(),
                TextColumn::make('litres')->numeric(decimalPlaces: 2)->suffix(' L'),
                TextColumn::make('price_per_litre')->money('GHS')->label('Price/L'),
                TextColumn::make('total_cost')->money('GHS')->sortable(),
                TextColumn::make('mileage_at_fill')->label('Mileage')->numeric(decimalPlaces: 0),
                TextColumn::make('fuel_station')->label('Station'),
                TextColumn::make('driver.name')->label('Driver'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
