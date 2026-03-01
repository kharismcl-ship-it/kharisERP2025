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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Fleet\Filament\Resources\VehicleResource\Pages;
use Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;
use Modules\Fleet\Models\Vehicle;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vehicle Identity')
                ->description('Name, plate, and classification')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Vehicle Name / Alias')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Company Bus 01'),
                    TextInput::make('plate')
                        ->label('Plate Number')
                        ->maxLength(50)
                        ->placeholder('e.g. GR-1234-24'),
                    Grid::make(3)->schema([
                        TextInput::make('make')
                            ->label('Make')
                            ->maxLength(100)
                            ->placeholder('e.g. Toyota'),
                        TextInput::make('model')
                            ->label('Model')
                            ->maxLength(100)
                            ->placeholder('e.g. Hilux'),
                        TextInput::make('year')
                            ->label('Year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1),
                    ])->columnSpanFull(),
                    Grid::make(3)->schema([
                        Select::make('type')
                            ->label('Vehicle Type')
                            ->options(array_combine(Vehicle::TYPES, array_map('ucfirst', Vehicle::TYPES)))
                            ->required()
                            ->default('car'),
                        TextInput::make('color')
                            ->label('Color')
                            ->maxLength(50),
                        Select::make('fuel_type')
                            ->label('Fuel Type')
                            ->options(array_combine(Vehicle::FUEL_TYPES, array_map('ucfirst', Vehicle::FUEL_TYPES)))
                            ->required()
                            ->default('petrol'),
                    ])->columnSpanFull(),
                ]),

            Section::make('Technical & Operational')
                ->description('Chassis, engine, capacity, mileage, and current status')
                ->columns(2)
                ->schema([
                    TextInput::make('chassis_number')
                        ->label('Chassis Number')
                        ->maxLength(100),
                    TextInput::make('engine_number')
                        ->label('Engine Number')
                        ->maxLength(100),
                    Grid::make(3)->schema([
                        TextInput::make('capacity')
                            ->label('Capacity (seats / tonnes)')
                            ->numeric()
                            ->step(0.01),
                        TextInput::make('current_mileage')
                            ->label('Current Mileage (km)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('km'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active'            => 'Active',
                                'inactive'          => 'Inactive',
                                'under_maintenance' => 'Under Maintenance',
                                'retired'           => 'Retired',
                            ])
                            ->required()
                            ->default('active'),
                    ])->columnSpanFull(),
                ]),

            Section::make('Purchase Information')
                ->description('Acquisition date, cost, and notes')
                ->collapsible()
                ->columns(2)
                ->schema([
                    DatePicker::make('purchase_date')
                        ->label('Purchase Date')
                        ->displayFormat('d M Y'),
                    TextInput::make('purchase_price')
                        ->label('Purchase Price')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01),
                    Textarea::make('description')
                        ->label('Additional Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate')->label('Plate')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('make')->searchable(),
                TextColumn::make('model'),
                TextColumn::make('year')->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'            => 'success',
                        'inactive'          => 'gray',
                        'under_maintenance' => 'warning',
                        'retired'           => 'danger',
                        default             => 'gray',
                    }),
                TextColumn::make('current_mileage')
                    ->label('Mileage (km)')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('fuel_type')->label('Fuel')->badge(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(array_combine(Vehicle::TYPES, array_map('ucfirst', Vehicle::TYPES))),
                SelectFilter::make('status')
                    ->options([
                        'active'            => 'Active',
                        'inactive'          => 'Inactive',
                        'under_maintenance' => 'Under Maintenance',
                        'retired'           => 'Retired',
                    ]),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MaintenanceRecordsRelationManager::class,
            RelationManagers\FuelLogsRelationManager::class,
            RelationManagers\TripLogsRelationManager::class,
            RelationManagers\DriverAssignmentsRelationManager::class,
            RelationManagers\VehicleDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'view'   => Pages\ViewVehicle::route('/{record}'),
            'edit'   => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
