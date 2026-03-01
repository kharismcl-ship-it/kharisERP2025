<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Fleet\Filament\Resources\TripLogResource\Pages;
use Modules\Fleet\Models\TripLog;

class TripLogResource extends Resource
{
    protected static ?string $model = TripLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 5;

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
                    DatePicker::make('trip_date')->required(),
                    TextInput::make('trip_reference')->label('Reference')->maxLength(50)->disabled(),
                    Select::make('status')
                        ->options(array_combine(TripLog::STATUSES, array_map('ucfirst', TripLog::STATUSES)))
                        ->default('completed'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('origin')->required()->maxLength(255),
                    TextInput::make('destination')->required()->maxLength(255),
                ]),
                TextInput::make('purpose')->maxLength(255)->columnSpanFull(),
                Grid::make(3)->schema([
                    TextInput::make('start_mileage')->label('Start Mileage (km)')->numeric()->step(0.01),
                    TextInput::make('end_mileage')->label('End Mileage (km)')->numeric()->step(0.01),
                    TextInput::make('distance_km')->label('Distance (km)')->numeric()->step(0.01)->disabled(),
                ]),
                Grid::make(2)->schema([
                    TimePicker::make('departure_time')->label('Departure Time'),
                    TimePicker::make('return_time')->label('Return Time'),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trip_date')->date()->sortable(),
                TextColumn::make('trip_reference')->label('Reference')->searchable(),
                TextColumn::make('vehicle.name')->label('Vehicle')->searchable(),
                TextColumn::make('vehicle.plate')->label('Plate'),
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(TripLog::STATUSES, array_map('ucfirst', TripLog::STATUSES))),
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
            ->defaultSort('trip_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTripLogs::route('/'),
            'create' => Pages\CreateTripLog::route('/create'),
            'edit'   => Pages\EditTripLog::route('/{record}/edit'),
        ];
    }
}
