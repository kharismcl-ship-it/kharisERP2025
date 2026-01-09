<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Enums\MaintenanceOutcome;
use Modules\Hostels\Enums\MaintenancePriority;
use Modules\Hostels\Enums\MaintenanceStatus;
use Modules\Hostels\Enums\MaintenanceType;
use Modules\Hostels\Filament\Resources\MaintenanceRecordResource\Pages;
use Modules\Hostels\Models\MaintenanceRecord;

class MaintenanceRecordResource extends Resource
{
    protected static ?string $model = MaintenanceRecord::class;

    protected static ?string $slug = 'inventory-maintenance-records';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventory_item_id')
                    ->relationship('inventoryItem', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Inventory Item'),

                Select::make('room_assignment_id')
                    ->relationship('roomAssignment', 'id')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label('Room Assignment'),

                Select::make('assigned_to')
                    ->relationship('assignedStaff', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label('Assigned Staff'),

                ToggleButtons::make('maintenance_type')
                    ->options(MaintenanceType::class)
                    ->required()
                    ->inline()
                    ->label('Maintenance Type'),

                ToggleButtons::make('priority')
                    ->options(MaintenancePriority::class)
                    ->required()
                    ->inline()
                    ->label('Priority'),

                ToggleButtons::make('status')
                    ->options(MaintenanceStatus::class)
                    ->required()
                    ->inline()
                    ->label('Status'),

                DateTimePicker::make('scheduled_date')
                    ->nullable()
                    ->label('Scheduled Date'),

                DateTimePicker::make('started_at')
                    ->nullable()
                    ->label('Started At'),

                DateTimePicker::make('completed_at')
                    ->nullable()
                    ->label('Completed At'),

                Textarea::make('description')
                    ->rows(3)
                    ->nullable()
                    ->label('Description'),

                Textarea::make('issue_details')
                    ->rows(3)
                    ->nullable()
                    ->label('Issue Details'),

                Textarea::make('work_performed')
                    ->rows(3)
                    ->nullable()
                    ->label('Work Performed'),

                Textarea::make('parts_used')
                    ->rows(2)
                    ->nullable()
                    ->label('Parts Used (JSON)'),

                TextInput::make('labor_cost')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->label('Labor Cost'),

                TextInput::make('parts_cost')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->label('Parts Cost'),

                TextInput::make('total_cost')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->label('Total Cost'),

                ToggleButtons::make('outcome')
                    ->options(MaintenanceOutcome::class)
                    ->nullable()
                    ->inline()
                    ->label('Outcome'),

                Textarea::make('notes')
                    ->rows(2)
                    ->nullable()
                    ->label('Notes'),

                Textarea::make('follow_up_required')
                    ->rows(2)
                    ->nullable()
                    ->label('Follow Up Required'),

                DateTimePicker::make('next_maintenance_date')
                    ->nullable()
                    ->label('Next Maintenance Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventoryItem.name')
                    ->searchable()
                    ->sortable()
                    ->label('Inventory Item'),

                TextColumn::make('roomAssignment.room.room_number')
                    ->searchable()
                    ->sortable()
                    ->label('Room'),

                TextColumn::make('maintenance_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'preventive' => 'info',
                        'corrective' => 'warning',
                        'emergency' => 'danger',
                        'routine' => 'success',
                        default => 'gray',
                    })
                    ->label('Type'),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'blue',
                        'high' => 'orange',
                        'critical' => 'red',
                        default => 'gray',
                    })
                    ->label('Priority'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'blue',
                        'completed' => 'green',
                        'cancelled' => 'red',
                        default => 'gray',
                    })
                    ->label('Status'),

                TextColumn::make('scheduled_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Scheduled'),

                TextColumn::make('assignedStaff.name')
                    ->searchable()
                    ->sortable()
                    ->label('Assigned To'),

                TextColumn::make('total_cost')
                    ->money('GHS')
                    ->sortable()
                    ->label('Cost'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // Create action is handled in pages
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceRecords::route('/'),
            'create' => Pages\CreateMaintenanceRecord::route('/create'),
            'edit' => Pages\EditMaintenanceRecord::route('/{record}/edit'),
        ];
    }
}
