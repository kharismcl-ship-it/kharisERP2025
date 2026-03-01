<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables;
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
                ->label('Driver (User)')
                ->relationship('user', 'name')
                ->searchable()
                ->nullable(),
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
                TextColumn::make('assigned_until')->date()->label('Until')->default('Ongoing'),
                IconColumn::make('is_primary')->label('Primary')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('assigned_from', 'desc');
    }
}
