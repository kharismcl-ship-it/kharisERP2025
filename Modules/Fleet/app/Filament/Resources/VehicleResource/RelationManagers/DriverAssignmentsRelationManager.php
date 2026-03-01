<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class DriverAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'driverAssignments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Driver')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),
            DatePicker::make('assigned_from')->required(),
            DatePicker::make('assigned_until')->label('Assigned Until (leave blank for ongoing)')->nullable(),
            Toggle::make('is_primary')->label('Primary Driver')->default(true),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Driver'),
                TextColumn::make('assigned_from')->date()->sortable(),
                TextColumn::make('assigned_until')->date()->label('Until')->placeholder('Ongoing'),
                IconColumn::make('is_primary')->label('Primary')->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('assigned_from', 'desc');
    }
}
