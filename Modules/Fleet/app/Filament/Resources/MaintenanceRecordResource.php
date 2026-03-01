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
use Modules\Fleet\Filament\Resources\MaintenanceRecordResource\Pages;
use Modules\Fleet\Models\MaintenanceRecord;

class MaintenanceRecordResource extends Resource
{
    protected static ?string $model = MaintenanceRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service Details')
                ->description('Vehicle, service type, and provider information')
                ->columns(2)
                ->schema([
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('type')
                        ->label('Service Type')
                        ->options(array_combine(
                            MaintenanceRecord::TYPES,
                            array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MaintenanceRecord::TYPES))
                        ))
                        ->required(),
                    TextInput::make('service_provider')
                        ->label('Service Provider / Garage')
                        ->maxLength(255)
                        ->placeholder('e.g. ABC Auto Workshop'),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'scheduled'   => 'Scheduled',
                            'in_progress' => 'In Progress',
                            'completed'   => 'Completed',
                        ])
                        ->default('scheduled')
                        ->required(),
                ]),

            Section::make('Description')
                ->schema([
                    Textarea::make('description')
                        ->label('Service Description')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Describe the work being performed...'),
                ]),

            Section::make('Dates & Mileage')
                ->description('Service date, odometer, and next due schedule')
                ->columns(3)
                ->schema([
                    DatePicker::make('service_date')
                        ->label('Service Date')
                        ->required()
                        ->displayFormat('d M Y'),
                    TextInput::make('mileage_at_service')
                        ->label('Mileage at Service')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('km'),
                    TextInput::make('cost')
                        ->label('Estimated / Actual Cost')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->default(0),
                    DatePicker::make('next_service_date')
                        ->label('Next Service Due')
                        ->displayFormat('d M Y'),
                    TextInput::make('next_service_mileage')
                        ->label('Next Service Mileage')
                        ->numeric()
                        ->step(0.01)
                        ->suffix('km'),
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
                TextColumn::make('service_date')->date()->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('description')->limit(40),
                TextColumn::make('cost')->money('GHS')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'scheduled'   => 'info',
                        default       => 'gray',
                    }),
                TextColumn::make('service_provider')->label('Provider'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(array_combine(
                        MaintenanceRecord::TYPES,
                        array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MaintenanceRecord::TYPES))
                    )),
                SelectFilter::make('status')
                    ->options([
                        'scheduled'   => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
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
            ])
            ->defaultSort('service_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaintenanceRecords::route('/'),
            'create' => Pages\CreateMaintenanceRecord::route('/create'),
            'view'   => Pages\ViewMaintenanceRecord::route('/{record}'),
            'edit'   => Pages\EditMaintenanceRecord::route('/{record}/edit'),
        ];
    }
}
