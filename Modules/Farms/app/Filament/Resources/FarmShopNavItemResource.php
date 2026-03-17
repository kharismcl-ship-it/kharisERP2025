<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmShopNavItemResource\Pages;
use Modules\Farms\Models\FarmShopNavItem;

class FarmShopNavItemResource extends Resource
{
    protected static ?string $model = FarmShopNavItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?string $navigationLabel = 'Shop Nav Menu';

    protected static ?int $navigationSort = 36;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Navigation Item')
                ->columns(2)
                ->schema([
                    TextInput::make('label')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('About Us'),
                    TextInput::make('url')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('/farm-shop/pages/about-us'),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->default(true),
                    Toggle::make('opens_blank')
                        ->label('Open in New Tab')
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->searchable()->sortable(),
                TextColumn::make('url')->limit(60),
                IconColumn::make('opens_blank')->boolean()->label('New Tab'),
                TextColumn::make('sort_order')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $companyId = Filament::getTenant()?->id;
        return parent::getEloquentQuery()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmShopNavItems::route('/'),
            'create' => Pages\CreateFarmShopNavItem::route('/create'),
            'edit'   => Pages\EditFarmShopNavItem::route('/{record}/edit'),
        ];
    }
}
