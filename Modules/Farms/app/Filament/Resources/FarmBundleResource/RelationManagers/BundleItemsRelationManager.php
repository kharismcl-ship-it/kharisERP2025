<?php

namespace Modules\Farms\Filament\Resources\FarmBundleResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BundleItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'bundleItems';

    protected static ?string $title = 'Bundle Contents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('farm_produce_inventory_id')
                ->label('Product')
                ->relationship('product', 'product_name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('quantity')
                ->numeric()
                ->step(0.001)
                ->default(1)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.product_name')->label('Product'),
                TextColumn::make('product.unit')->label('Unit'),
                TextColumn::make('quantity'),
                TextColumn::make('product.unit_price')->money('GHS')->label('Unit Price'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([DeleteAction::make()]);
    }
}
