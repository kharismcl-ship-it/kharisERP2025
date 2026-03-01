<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TankLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'tankLevels';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('tank_name')->required()->maxLength(255),
                DateTimePicker::make('recorded_at')->required()->default(now()),
            ]),
            Grid::make(2)->schema([
                TextInput::make('capacity_liters')->label('Capacity (L)')->numeric()->step(0.01)->required(),
                TextInput::make('current_level_liters')->label('Current Level (L)')->numeric()->step(0.01)->required(),
            ]),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tank_name')->label('Tank')->searchable()->sortable(),
                TextColumn::make('capacity_liters')->label('Capacity (L)')->numeric(decimalPlaces: 0),
                TextColumn::make('current_level_liters')->label('Current (L)')->numeric(decimalPlaces: 0),
                TextColumn::make('fill_percentage')->label('Fill %')
                    ->getStateUsing(fn ($record) => $record->fill_percentage . '%'),
                TextColumn::make('recorded_at')->dateTime()->sortable(),
            ])
            ->defaultSort('recorded_at', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
