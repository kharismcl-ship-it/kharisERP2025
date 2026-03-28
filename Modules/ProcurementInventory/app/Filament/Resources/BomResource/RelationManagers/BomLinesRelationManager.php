<?php

namespace Modules\ProcurementInventory\Filament\Resources\BomResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BomLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Components';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('component_item_id')
                ->label('Component / Raw Material')
                ->relationship('componentItem', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('quantity_required')
                ->label('Quantity Required')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('unit_of_measure')
                ->label('UOM'),

            Forms\Components\TextInput::make('waste_factor_pct')
                ->label('Waste Factor (%)')
                ->numeric()
                ->default(0)
                ->suffix('%'),

            Forms\Components\TextInput::make('sort_order')
                ->label('Sort Order')
                ->numeric()
                ->default(0),

            Forms\Components\TextInput::make('notes')
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('componentItem.sku')
                    ->label('SKU'),

                Tables\Columns\TextColumn::make('componentItem.name')
                    ->label('Component'),

                Tables\Columns\TextColumn::make('quantity_required')
                    ->label('Qty Required')
                    ->numeric(4),

                Tables\Columns\TextColumn::make('unit_of_measure')
                    ->label('UOM')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('waste_factor_pct')
                    ->label('Waste %')
                    ->suffix('%')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('notes')
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}