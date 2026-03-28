<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmIotDeviceResource\Pages;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmIotDevice;
use Modules\Farms\Models\FarmPlot;
use Filament\Facades\Filament;

class FarmIotDeviceResource extends Resource
{
    protected static ?string $model = FarmIotDevice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|\UnitEnum|null $navigationGroup = 'Precision Agriculture';

    protected static ?string $navigationLabel = 'IoT Devices';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Device Details')
                ->columns(2)
                ->schema([
                    TextInput::make('device_name')->required()->maxLength(255),

                    Select::make('device_type')
                        ->options([
                            'soil_moisture'   => 'Soil Moisture',
                            'weather_station' => 'Weather Station',
                            'temperature'     => 'Temperature',
                            'humidity'        => 'Humidity',
                            'water_flow'      => 'Water Flow',
                            'ph_sensor'       => 'pH Sensor',
                            'other'           => 'Other',
                        ])
                        ->required(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->required(),

                    Select::make('farm_plot_id')
                        ->label('Plot (optional)')
                        ->options(fn (Get $get) => FarmPlot::where('farm_id', $get('farm_id'))->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    TextInput::make('manufacturer')->maxLength(255),
                    TextInput::make('model_number')->maxLength(255),
                    TextInput::make('serial_number')->maxLength(255),
                ]),

            Section::make('Location')
                ->columns(2)
                ->schema([
                    TextInput::make('latitude')->numeric()->step(0.00000001)->nullable(),
                    TextInput::make('longitude')->numeric()->step(0.00000001)->nullable(),
                ]),

            Section::make('API Configuration')
                ->columns(2)
                ->schema([
                    TextInput::make('api_endpoint')->label('API Endpoint')->url()->maxLength(500)->nullable(),
                    TextInput::make('reading_interval_minutes')->label('Reading Interval (min)')->numeric()->default(60),
                ]),

            Section::make('Status')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'active'      => 'Active',
                            'offline'     => 'Offline',
                            'maintenance' => 'Maintenance',
                        ])
                        ->default('active')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_name')->sortable()->searchable(),
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('device_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('last_reading_value')
                    ->label('Last Reading')
                    ->formatStateUsing(fn ($state, FarmIotDevice $record): string => $state !== null ? (string) $state : '—')
                    ->placeholder('—'),
                TextColumn::make('last_reading_at')->label('Last Reading At')->dateTime()->sortable()->placeholder('—'),
                TextColumn::make('battery_pct')
                    ->label('Battery')
                    ->formatStateUsing(fn ($state): string => $state !== null ? round((float) $state) . '%' : '—')
                    ->color(fn ($state): string => $state !== null && $state < 20 ? 'danger' : 'success')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'      => 'success',
                        'offline'     => 'danger',
                        'maintenance' => 'warning',
                        default       => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'active'      => 'Active',
                    'offline'     => 'Offline',
                    'maintenance' => 'Maintenance',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmIotDevices::route('/'),
            'create' => Pages\CreateFarmIotDevice::route('/create'),
            'edit'   => Pages\EditFarmIotDevice::route('/{record}/edit'),
        ];
    }
}