<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class MaterialUsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'materialUsages';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('material_name')->required()->maxLength(255),
            Select::make('project_phase_id')
                ->label('Phase')
                ->relationship('phase', 'name')
                ->searchable()
                ->nullable(),
            DatePicker::make('usage_date')->required(),
            TextInput::make('unit')->maxLength(50),
            TextInput::make('quantity')->required()->numeric()->step(0.001),
            TextInput::make('unit_cost')->label('Unit Cost')->numeric()->prefix('GHS')->step(0.0001),
            TextInput::make('total_cost')->label('Total Cost')->numeric()->prefix('GHS')->step(0.01),
            TextInput::make('supplier')->maxLength(255),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('usage_date')->date()->sortable(),
                TextColumn::make('material_name')->label('Material')->searchable(),
                TextColumn::make('phase.name')->label('Phase'),
                TextColumn::make('quantity')->numeric(decimalPlaces: 2),
                TextColumn::make('unit'),
                TextColumn::make('unit_cost')->money('GHS')->label('Unit Cost'),
                TextColumn::make('total_cost')->money('GHS')->sortable(),
                TextColumn::make('supplier'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('usage_date', 'desc');
    }
}
