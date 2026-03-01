<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\PriceListResource\Pages;
use Modules\Sales\Models\SalesPriceList;

class PriceListResource extends Resource
{
    protected static ?string $model = SalesPriceList::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-tag';
    protected static string|\UnitEnum|null   $navigationGroup = 'Catalog';
    protected static ?int                    $navigationSort  = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Price List')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('currency')->default('GHS')->maxLength(10),
                DatePicker::make('valid_from'),
                DatePicker::make('valid_to'),
                Toggle::make('is_default')->label('Set as Default')->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('currency'),
                TextColumn::make('valid_from')->date(),
                TextColumn::make('valid_to')->date(),
                ToggleColumn::make('is_default')->label('Default'),
                TextColumn::make('items_count')->counts('items')->label('Items'),
            ])
            ->recordActions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPriceLists::route('/'),
            'create' => Pages\CreatePriceList::route('/create'),
            'edit'   => Pages\EditPriceList::route('/{record}/edit'),
        ];
    }
}