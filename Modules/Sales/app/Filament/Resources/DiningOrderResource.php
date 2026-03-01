<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\DiningOrderResource\Pages;
use Modules\Sales\Models\DiningOrder;

class DiningOrderResource extends Resource
{
    protected static ?string $model = DiningOrder::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null   $navigationGroup = 'Restaurant';
    protected static ?int                    $navigationSort  = 51;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dining Order')->columns(2)->schema([
                Select::make('table_id')
                    ->label('Table')
                    ->relationship('table', 'table_number')
                    ->required()->searchable()->preload(),
                Select::make('waiter_id')
                    ->label('Waiter')
                    ->relationship('waiter', 'name')
                    ->searchable()->preload(),
                Select::make('status')
                    ->options(array_combine(DiningOrder::STATUSES, array_map('ucfirst', DiningOrder::STATUSES)))
                    ->default('open'),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
            Section::make('Items')->schema([
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Select::make('catalog_item_id')
                            ->label('Item')
                            ->relationship('catalogItem', 'name')
                            ->searchable()->preload()->required()
                            ->columnSpan(4),
                        TextInput::make('quantity')->numeric()->default(1)->columnSpan(1),
                        TextInput::make('unit_price')->numeric()->prefix('GHS')->columnSpan(2),
                        TextInput::make('line_total')->numeric()->disabled()->dehydrated(false)->columnSpan(2),
                        Select::make('status')
                            ->options(array_combine(\Modules\Sales\Models\DiningOrderItem::STATUSES, array_map('ucfirst', \Modules\Sales\Models\DiningOrderItem::STATUSES)))
                            ->default('pending')
                            ->columnSpan(1),
                        Textarea::make('notes')->rows(1)->columnSpanFull(),
                    ])
                    ->columns(10)
                    ->defaultItems(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('table.table_number')->label('Table'),
                TextColumn::make('table.restaurant.name')->label('Restaurant'),
                TextColumn::make('waiter.name')->label('Waiter'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid'       => 'success',
                        'cancelled'  => 'danger',
                        'ready'      => 'warning',
                        'in_kitchen' => 'info',
                        'served'     => 'primary',
                        default      => 'gray',
                    }),
                TextColumn::make('total')->money('GHS')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(array_combine(DiningOrder::STATUSES, array_map('ucfirst', DiningOrder::STATUSES))),
            ])
            ->recordActions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDiningOrders::route('/'),
            'create' => Pages\CreateDiningOrder::route('/create'),
            'edit'   => Pages\EditDiningOrder::route('/{record}/edit'),
        ];
    }
}