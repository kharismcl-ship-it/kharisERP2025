<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\StockLevelResource\Pages;
use Modules\ProcurementInventory\Models\StockLevel;

class StockLevelResource extends Resource
{
    protected static ?string $model = StockLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement & Inventory';

    protected static ?int $navigationSort = 60;

    protected static ?string $label = 'Stock Level';

    protected static ?string $pluralLabel = 'Stock Levels';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('item.name')
            ->columns([
                Tables\Columns\TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.category.name')
                    ->label('Category')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('item.unit_of_measure')
                    ->label('UOM'),

                Tables\Columns\TextColumn::make('quantity_on_hand')
                    ->label('On Hand')
                    ->numeric(4)
                    ->sortable()
                    ->color(fn (StockLevel $record): string => $record->needsReorder() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('quantity_reserved')
                    ->label('Reserved')
                    ->numeric(4)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity_on_order')
                    ->label('On Order')
                    ->numeric(4)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Available')
                    ->numeric(4)
                    ->getStateUsing(fn (StockLevel $record) => $record->available_quantity),

                Tables\Columns\TextColumn::make('item.reorder_level')
                    ->label('Reorder Level')
                    ->numeric(4)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('needs_reorder')
                    ->label('Needs Reorder')
                    ->boolean()
                    ->getStateUsing(fn (StockLevel $record) => $record->needsReorder()),

                Tables\Columns\TextColumn::make('last_counted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('item.category', 'name')
                    ->label('Category'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockLevels::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Stock levels are created automatically
    }
}
