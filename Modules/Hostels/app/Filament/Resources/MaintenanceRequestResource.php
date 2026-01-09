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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\MaintenanceRequestResource\Pages;
use Modules\Hostels\Models\MaintenanceRequest;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static ?string $slug = 'maintenance-requests';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Maintenance Request Details')
                    ->schema([
                        Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->searchable()
                            ->required(),

                    Select::make('room_id')
                        ->relationship('room', 'room_number')
                        ->searchable()
                        ->nullable(),

                    Select::make('bed_id')
                        ->relationship('bed', 'bed_number')
                        ->searchable()
                        ->nullable(),

                    Select::make('reported_by_hostel_occupant_id')
                        ->relationship('reportedByHostelOccupant', 'first_name')
                        ->searchable()
                        ->nullable(),

                    Select::make('reported_by_user_id')
                        ->relationship('reportedByUser', 'name')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('title')
                        ->required(),
                ]),

                Textarea::make('description')
                    ->required(),

                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('open'),

                Select::make('assigned_to_user_id')
                    ->relationship('assignedToUser', 'name')
                    ->searchable()
                    ->nullable(),

                DateTimePicker::make('reported_at')
                    ->required(),

                DateTimePicker::make('completed_at')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room.room_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('bed.bed_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('priority')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignedToUser.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('reported_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceRequests::route('/'),
            'create' => Pages\CreateMaintenanceRequest::route('/create'),
            'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
