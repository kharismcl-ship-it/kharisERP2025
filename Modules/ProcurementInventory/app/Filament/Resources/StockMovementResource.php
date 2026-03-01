<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\StockMovementResource\Pages;
use Modules\ProcurementInventory\Models\StockMovement;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 70;

    protected static ?string $label = 'Stock Movement';

    protected static ?string $pluralLabel = 'Stock Movements';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),

                Tables\Columns\TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'receipt'    => 'success',
                        'adjustment' => 'warning',
                        'issue'      => 'danger',
                        'transfer'   => 'info',
                        'return'     => 'gray',
                        'opening'    => 'primary',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => StockMovement::TYPES[$state] ?? $state),

                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(4)
                    ->color(fn (StockMovement $record): string => (float) $record->quantity >= 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('quantity_before')
                    ->label('Before')
                    ->numeric(4)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('After')
                    ->numeric(4),

                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('note')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options(StockMovement::TYPES),
                Tables\Filters\SelectFilter::make('item')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
