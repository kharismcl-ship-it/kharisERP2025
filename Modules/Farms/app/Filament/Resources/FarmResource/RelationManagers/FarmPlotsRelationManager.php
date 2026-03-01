<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\FarmPlot;

class FarmPlotsRelationManager extends RelationManager
{
    protected static string $relationship = 'plots';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('area')->numeric()->step(0.0001),
            Select::make('area_unit')->options(['acres' => 'Acres', 'hectares' => 'Hectares'])->default('acres'),
            Select::make('soil_type')->options(array_combine(FarmPlot::SOIL_TYPES, array_map('ucfirst', FarmPlot::SOIL_TYPES)))->nullable(),
            Select::make('status')->options(array_combine(FarmPlot::STATUSES, array_map('ucfirst', FarmPlot::STATUSES)))->default('active'),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('area')->numeric(decimalPlaces: 2),
                TextColumn::make('area_unit')->label('Unit'),
                TextColumn::make('soil_type')->label('Soil'),
                TextColumn::make('status')->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
