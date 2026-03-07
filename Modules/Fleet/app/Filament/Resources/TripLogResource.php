<?php

namespace Modules\Fleet\Filament\Resources;

use EduardoRibeiroDev\FilamentLeaflet\Fields\MapPicker;
use EduardoRibeiroDev\FilamentLeaflet\Tables\MapColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
            Section::make('Trip Identity')
                ->description('Vehicle, driver, and trip date')
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
                    DatePicker::make('trip_date')
                        ->label('Trip Date')
                        ->required()
                        ->displayFormat('d M Y'),
                    Select::make('status')
                        ->label('Status')
                        ->options(array_combine(TripLog::STATUSES, array_map('ucfirst', TripLog::STATUSES)))
                        ->default('planned')
                        ->required(),
                ]),

            Section::make('Route')
                ->description('Origin, destination, and purpose')
                ->columns(2)
                ->schema([
                    TextInput::make('origin')
                        ->label('Origin / From')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Accra Head Office'),
                    TextInput::make('destination')
                        ->label('Destination / To')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Kumasi Branch'),
                    TextInput::make('purpose')
                        ->label('Trip Purpose')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->placeholder('e.g. Client meeting, goods delivery'),
                ]),

            Section::make('Route Map')
                ->icon('heroicon-o-map')
                ->description('Pin origin and destination on the map')
                ->collapsible()
                ->columns(2)
                ->schema([
                    MapPicker::make('origin_map')
                        ->label('Origin Pin')
                        ->latitudeFieldName('origin_lat')
                        ->longitudeFieldName('origin_lng')
                        ->center(5.6037, -0.1870)
                        ->height(300)
                        ->zoom(12)
                        ->fullscreenControl()
                        ->searchControl()
                        ->scaleControl()
                        ->columnSpanFull(),
                    TextInput::make('origin_lat')->numeric()->readOnly()->label('Origin Latitude')->placeholder('Auto-filled'),
                    TextInput::make('origin_lng')->numeric()->readOnly()->label('Origin Longitude')->placeholder('Auto-filled'),

                    MapPicker::make('destination_map')
                        ->label('Destination Pin')
                        ->latitudeFieldName('destination_lat')
                        ->longitudeFieldName('destination_lng')
                        ->center(5.6037, -0.1870)
                        ->height(300)
                        ->zoom(12)
                        ->fullscreenControl()
                        ->searchControl()
                        ->scaleControl()
                        ->columnSpanFull(),
                    TextInput::make('destination_lat')->numeric()->readOnly()->label('Destination Latitude')->placeholder('Auto-filled'),
                    TextInput::make('destination_lng')->numeric()->readOnly()->label('Destination Longitude')->placeholder('Auto-filled'),
                ]),

            Section::make('Mileage & Timing')
                ->description('Odometer readings and departure / return times')
                ->columns(3)
                ->schema([
                    TextInput::make('start_mileage')
                        ->label('Start Mileage (km)')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('km'),
                    TextInput::make('end_mileage')
                        ->label('End Mileage (km)')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('km'),
                    TextInput::make('distance_km')
                        ->label('Distance (km)')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('km')
                        ->disabled()
                        ->helperText('Auto-calculated on save'),
                    TimePicker::make('departure_time')
                        ->label('Departure Time'),
                    TimePicker::make('return_time')
                        ->label('Return Time'),
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
                MapColumn::make('origin_lat')
                    ->label('Origin Map')
                    ->latitudeFieldName('origin_lat')
                    ->longitudeFieldName('origin_lng')
                    ->height(80)
                    ->zoom(13)
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                MapColumn::make('destination_lat')
                    ->label('Destination Map')
                    ->latitudeFieldName('destination_lat')
                    ->longitudeFieldName('destination_lng')
                    ->height(80)
                    ->zoom(13)
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(TripLog::STATUSES, array_map('ucfirst', TripLog::STATUSES))),
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
            ->defaultSort('trip_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTripLogs::route('/'),
            'create' => Pages\CreateTripLog::route('/create'),
            'view'   => Pages\ViewTripLog::route('/{record}'),
            'edit'   => Pages\EditTripLog::route('/{record}/edit'),
        ];
    }
}
