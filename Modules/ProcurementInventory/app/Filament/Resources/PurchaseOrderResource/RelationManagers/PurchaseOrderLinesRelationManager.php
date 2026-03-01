<?php

namespace Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Order Lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('item_id')
                ->relationship('item', 'name')
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $item = \Modules\ProcurementInventory\Models\Item::find($state);
                        if ($item) {
                            $set('description', $item->name);
                            $set('unit_of_measure', $item->unit_of_measure);
                            $set('unit_price', $item->unit_price);
                        }
                    }
                }),

            Forms\Components\TextInput::make('description')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->required()
                ->default(1)
                ->step(0.0001),

            Forms\Components\TextInput::make('unit_of_measure')
                ->placeholder('pcs, kg…'),

            Forms\Components\TextInput::make('unit_price')
                ->numeric()
                ->required()
                ->prefix('GHS')
                ->step(0.0001),

            Forms\Components\TextInput::make('tax_rate')
                ->numeric()
                ->default(0)
                ->suffix('%'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.sku')->label('SKU')->toggleable(),
                Tables\Columns\TextColumn::make('description')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('quantity')->numeric(4),
                Tables\Columns\TextColumn::make('unit_of_measure')->label('UOM'),
                Tables\Columns\TextColumn::make('unit_price')->money('GHS'),
                Tables\Columns\TextColumn::make('tax_rate')->suffix('%'),
                Tables\Columns\TextColumn::make('line_total')->money('GHS')->weight('bold'),
                Tables\Columns\TextColumn::make('quantity_received')->label('Received')->numeric(4),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => in_array($this->ownerRecord->status, ['draft'])),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => $this->ownerRecord->status === 'draft'),
                DeleteAction::make()
                    ->visible(fn () => $this->ownerRecord->status === 'draft'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn () => $this->ownerRecord->status === 'draft'),
            ]);
    }
}