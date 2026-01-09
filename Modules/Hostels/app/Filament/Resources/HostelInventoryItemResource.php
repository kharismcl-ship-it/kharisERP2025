<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\HostelInventoryItem;

class HostelInventoryItemResource extends Resource
{
    protected static ?string $model = HostelInventoryItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('hostel_id')
                    ->label('Hostel')
                    ->relationship('hostel', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->label('Item Name')
                    ->required()
                    ->maxLength(180),

                Select::make('category')
                    ->label('Category')
                    ->options([
                        'linen' => 'Linens',
                        'furniture' => 'Furniture',
                        'equipment' => 'Equipment',
                        'consumables' => 'Consumables',
                        'cleaning' => 'Cleaning Supplies',
                        'maintenance' => 'Maintenance Tools',
                    ])
                    ->required(),

                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                TextInput::make('sku')
                    ->label('SKU')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('unit_cost')
                    ->label('Unit Cost')
                    ->numeric()
                    ->prefix('GHS')
                    ->default(0),

                TextInput::make('current_stock')
                    ->label('Current Stock')
                    ->numeric()
                    ->default(0),

                TextInput::make('min_stock_level')
                    ->label('Minimum Stock Level')
                    ->numeric()
                    ->default(0),

                TextInput::make('max_stock_level')
                    ->label('Maximum Stock Level')
                    ->numeric()
                    ->nullable(),

                TextInput::make('uom')
                    ->label('Unit of Measure')
                    ->default('pcs')
                    ->maxLength(20),

                Toggle::make('status')
                    ->label('Active')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->label('Hostel')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Item Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'linen' => 'info',
                        'furniture' => 'warning',
                        'equipment' => 'primary',
                        'consumables' => 'success',
                        'cleaning' => 'gray',
                        'maintenance' => 'danger',
                        default => 'secondary',
                    }),

                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->sortable(),

                TextColumn::make('min_stock_level')
                    ->label('Min Level')
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('GHS')
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('hostel_id')
                    ->label('Hostel')
                    ->relationship('hostel', 'name'),

                \Filament\Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'linen' => 'Linens',
                        'furniture' => 'Furniture',
                        'equipment' => 'Equipment',
                        'consumables' => 'Consumables',
                        'cleaning' => 'Cleaning Supplies',
                        'maintenance' => 'Maintenance Tools',
                    ]),

                \Filament\Tables\Filters\TernaryFilter::make('status')
                    ->label('Active Status'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relation managers for transactions and room assignments
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Hostels\Filament\Resources\HostelInventoryItemResource\Pages\ListHostelInventoryItems::route('/'),
            'create' => \Modules\Hostels\Filament\Resources\HostelInventoryItemResource\Pages\CreateHostelInventoryItem::route('/create'),
            'edit' => \Modules\Hostels\Filament\Resources\HostelInventoryItemResource\Pages\EditHostelInventoryItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('hostel');
    }
}
