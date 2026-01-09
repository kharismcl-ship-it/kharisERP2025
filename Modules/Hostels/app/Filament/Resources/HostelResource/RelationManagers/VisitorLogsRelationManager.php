<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitorLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'visitorLogs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('visitor_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('visitor_phone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('purpose')
                    ->required()
                    ->maxLength(65535),

                DateTimePicker::make('check_in_at')
                    ->required(),

                DateTimePicker::make('check_out_at'),

                Select::make('hostel_occupant_id')
                    ->relationship('hostelOccupant', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visitor_name')
            ->columns([
                TextColumn::make('visitor_name')
                    ->searchable(),
                TextColumn::make('visitor_phone')
                    ->searchable(),
                TextColumn::make('purpose')
                    ->limit(50),
                TextColumn::make('check_in_at')
                    ->dateTime(),
                TextColumn::make('check_out_at')
                    ->dateTime(),
                TextColumn::make('hostelOccupant.name')
                    ->label('Hostel Occupant')
                    ->searchable(),
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
