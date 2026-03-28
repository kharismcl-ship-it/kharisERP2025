<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\StockLotResource\Pages;
use Modules\ProcurementInventory\Models\StockLot;

class StockLotResource extends Resource
{
    protected static ?string $model = StockLot::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 65;

    protected static ?string $label = 'Stock Lot';

    protected static ?string $pluralLabel = 'Stock Lots';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('lot_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('batch_number')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable(),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('quantity_on_hand')
                    ->label('Qty on Hand')
                    ->numeric(4),

                Tables\Columns\TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('GHS'),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->placeholder('—')
                    ->color(fn (StockLot $record): string => $record->isExpired() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available'  => 'success',
                        'quarantine' => 'warning',
                        'consumed'   => 'gray',
                        'expired'    => 'danger',
                        default      => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('item')
                    ->relationship('item', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available'  => 'Available',
                        'quarantine' => 'Quarantine',
                        'consumed'   => 'Consumed',
                        'expired'    => 'Expired',
                    ]),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring within 30 days')
                    ->query(fn ($query) => $query->whereNotNull('expiry_date')
                        ->where('expiry_date', '>=', now()->toDateString())
                        ->where('expiry_date', '<=', now()->addDays(30)->toDateString())),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockLots::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}