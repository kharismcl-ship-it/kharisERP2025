<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource;
use Modules\ProcurementInventory\Filament\Resources\StockLevelResource\Pages;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Services\StockService;

class StockLevelResource extends Resource
{
    protected static ?string $model = StockLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

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
                ViewAction::make(),

                Action::make('adjust')
                    ->label('Adjust')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('adjustment')
                            ->label('Quantity Adjustment')
                            ->helperText('Use positive to add, negative to reduce (e.g. -5)')
                            ->numeric()
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Reason / Note')
                            ->rows(2)
                            ->required(),
                    ])
                    ->action(function (StockLevel $record, array $data) {
                        $updated = app(StockService::class)->adjust(
                            $record->company_id,
                            $record->item_id,
                            (float) $data['adjustment'],
                            $data['note']
                        );
                        Notification::make()
                            ->title('Stock adjusted to ' . number_format((float) $updated->quantity_on_hand, 2))
                            ->success()
                            ->send();
                    }),

                Action::make('reorder')
                    ->label('Reorder Now')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('info')
                    ->visible(fn (StockLevel $record) => $record->needsReorder())
                    ->url(fn (StockLevel $record) => PurchaseOrderResource::getUrl('create')),
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
