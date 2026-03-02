<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\PosSaleResource\Pages;
use Modules\Sales\Models\PosSale;

class PosSaleResource extends Resource
{
    protected static ?string $model = PosSale::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-receipt-percent';
    protected static string|\UnitEnum|null   $navigationGroup = 'POS';
    protected static ?int                    $navigationSort  = 42;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('POS Sale')->columns(2)->schema([
                TextInput::make('reference')->disabled()->dehydrated(false)->placeholder('Auto-generated'),
                Select::make('session_id')
                    ->label('Session')
                    ->relationship('session', 'id')
                    ->required(),
                Select::make('contact_id')
                    ->label('Customer (optional)')
                    ->relationship('contact', 'first_name')
                    ->searchable()->preload(),
                TextInput::make('total')->numeric()->prefix('GHS')->disabled()->dehydrated(false),
            ]),
            Section::make('Items')->schema([
                Repeater::make('lines')
                    ->relationship()
                    ->schema([
                        Select::make('catalog_item_id')
                            ->label('Item')
                            ->relationship('catalogItem', 'name')
                            ->searchable()->preload()->required()
                            ->columnSpan(4),
                        TextInput::make('quantity')->numeric()->default(1)->columnSpan(1),
                        TextInput::make('unit_price')->numeric()->prefix('GHS')->columnSpan(2),
                        TextInput::make('discount_pct')->numeric()->suffix('%')->default(0)->columnSpan(1),
                        TextInput::make('line_total')->numeric()->disabled()->dehydrated(false)->columnSpan(2),
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
                TextColumn::make('reference')->searchable()->sortable(),
                TextColumn::make('session.terminal.name')->label('Terminal'),
                TextColumn::make('contact.first_name')->label('Customer'),
                TextColumn::make('total')->money('GHS')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosSales::route('/'),
            'create' => Pages\CreatePosSale::route('/create'),
            'edit'   => Pages\EditPosSale::route('/{record}/edit'),
        ];
    }
}