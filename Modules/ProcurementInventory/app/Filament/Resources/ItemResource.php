<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\ProcurementInventory\Filament\Resources\ItemResource\Pages;
use Modules\ProcurementInventory\Models\Item;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('company_id')
                ->relationship('company', 'name')
                
                ->searchable(),

            Forms\Components\Select::make('item_category_id')
                ->relationship('category', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

            Forms\Components\TextInput::make('sku')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('type')
                ->options([
                    'product'      => 'Product',
                    'service'      => 'Service',
                    'raw_material' => 'Raw Material',
                    'asset'        => 'Asset',
                ])
                ->default('product')
                ->required(),

            Forms\Components\TextInput::make('unit_of_measure')
                ->placeholder('pcs, kg, litre, box…')
                ->maxLength(50),

            Forms\Components\TextInput::make('unit_price')
                ->numeric()
                ->prefix('GHS')
                ->step(0.0001),

            Forms\Components\TextInput::make('reorder_level')
                ->numeric()
                ->label('Reorder Level'),

            Forms\Components\TextInput::make('reorder_quantity')
                ->numeric()
                ->label('Reorder Quantity'),

            Forms\Components\Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'product'      => 'info',
                        'service'      => 'warning',
                        'raw_material' => 'success',
                        'asset'        => 'gray',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('unit_of_measure')
                    ->label('UOM'),

                Tables\Columns\TextColumn::make('unit_price')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stockLevel.quantity_on_hand')
                    ->label('On Hand')
                    ->default(0)
                    ->numeric(2),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'product'      => 'Product',
                        'service'      => 'Service',
                        'raw_material' => 'Raw Material',
                        'asset'        => 'Asset',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'view'   => Pages\ViewItem::route('/{record}'),
            'edit'   => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku'];
    }
}
