<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaintenanceRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceRequests';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('room_id')
                    ->relationship('room', 'room_number')
                    ->searchable()
                    ->preload(),
                Select::make('bed_id')
                    ->relationship('bed', 'bed_number')
                    ->searchable()
                    ->preload(),
                Select::make('reported_by_hostel_occupant_id')
                    ->relationship('reportedByHostelOccupant', 'full_name')
                    ->searchable()
                    ->preload()
                    ->label('Reported by Hostel Occupant'),
                Select::make('reported_by_user_id')
                    ->relationship('reportedByUser', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Reported by User'),
                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->required()
                    ->default('medium'),
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
                    ->preload()
                    ->label('Assigned To'),
                DatePicker::make('reported_at'),
                DatePicker::make('completed_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('room.room_number')
                    ->label('Room'),
                TextColumn::make('bed.bed_number')
                    ->label('Bed'),
                TextColumn::make('reportedByTenant.full_name')
                    ->label('Reported by Tenant'),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reported_at')
                    ->dateTime(),
                TextColumn::make('completed_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
