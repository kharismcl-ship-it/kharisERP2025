<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Fleet\Models\TripLog;

class TripLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'tripLogs';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('trip_reference')->label('Reference')->maxLength(50)->disabled(),
            DatePicker::make('trip_date')->required(),
            TextInput::make('origin')->required()->maxLength(255),
            TextInput::make('destination')->required()->maxLength(255),
            TextInput::make('purpose')->maxLength(255),
            TextInput::make('start_mileage')->label('Start Mileage')->numeric()->step(0.01),
            TextInput::make('end_mileage')->label('End Mileage')->numeric()->step(0.01),
            TextInput::make('distance_km')->label('Distance (km)')->numeric()->step(0.01)->disabled(),
            TimePicker::make('departure_time')->label('Departure Time'),
            TimePicker::make('return_time')->label('Return Time'),
            Select::make('status')
                ->options(array_combine(
                    TripLog::STATUSES,
                    array_map('ucfirst', TripLog::STATUSES)
                ))
                ->default('completed'),
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
                TextColumn::make('trip_date')->date()->sortable(),
                TextColumn::make('trip_reference')->label('Reference'),
                TextColumn::make('origin'),
                TextColumn::make('destination'),
                TextColumn::make('distance_km')->label('Distance (km)')->numeric(decimalPlaces: 1),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'planned'     => 'info',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
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
            ->defaultSort('trip_date', 'desc');
    }
}
