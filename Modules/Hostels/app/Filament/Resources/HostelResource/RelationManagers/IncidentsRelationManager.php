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

class IncidentsRelationManager extends RelationManager
{
    protected static string $relationship = 'incidents';

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
                Select::make('hostel_occupant_id')
                    ->relationship('hostelOccupant', 'full_name')
                    ->searchable()
                    ->preload(),
                Select::make('room_id')
                    ->relationship('room', 'room_number')
                    ->searchable()
                    ->preload(),
                Select::make('reported_by_user_id')
                    ->relationship('reportedByUser', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Reported by User'),
                Textarea::make('action_taken')
                    ->columnSpanFull(),
                Select::make('severity')
                    ->options([
                        'minor' => 'Minor',
                        'major' => 'Major',
                        'critical' => 'Critical',
                    ])
                    ->required()
                    ->default('minor'),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'resolved' => 'Resolved',
                        'escalated' => 'Escalated',
                    ])
                    ->required()
                    ->default('open'),
                DatePicker::make('reported_at'),
                DatePicker::make('resolved_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('hostelOccupant.full_name')
                    ->label('Hostel Occupant'),
                TextColumn::make('room.room_number')
                    ->label('Room'),
                TextColumn::make('reportedByUser.name')
                    ->label('Reported by'),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'minor' => 'success',
                        'major' => 'warning',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'info',
                        'resolved' => 'success',
                        'escalated' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reported_at')
                    ->dateTime(),
                TextColumn::make('resolved_at')
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
