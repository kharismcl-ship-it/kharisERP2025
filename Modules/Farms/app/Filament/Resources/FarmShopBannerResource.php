<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmShopBannerResource\Pages;
use Modules\Farms\Models\FarmShopBanner;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;

class FarmShopBannerResource extends Resource
{
    protected static ?string $model = FarmShopBanner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?string $navigationLabel = 'Shop Banners';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Banner Content')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),
                    TextInput::make('subtitle')
                        ->maxLength(300)
                        ->columnSpanFull(),
                    TextInput::make('cta_text')
                        ->label('CTA Button Text')
                        ->placeholder('Shop Now')
                        ->maxLength(100),
                    TextInput::make('cta_url')
                        ->label('CTA Button URL')
                        ->placeholder('/farm-shop')
                        ->maxLength(500),
                    FileUpload::make('image_path')
                        ->label('Banner Image')
                        ->image()
                        ->directory('farm-shop/banners')
                        ->columnSpanFull(),
                    ColorPicker::make('overlay_color')
                        ->label('Overlay Color')
                        ->default('#000000'),
                    TextInput::make('overlay_opacity')
                        ->label('Overlay Opacity (0–100)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(30)
                        ->suffix('%'),
                ]),
            Section::make('Scheduling & Ordering')
                ->columns(2)
                ->schema([
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->label('Sort Order'),
                    Toggle::make('is_active')
                        ->default(true)
                        ->label('Active'),
                    DateTimePicker::make('starts_at')
                        ->label('Show From')
                        ->native(false),
                    DateTimePicker::make('ends_at')
                        ->label('Show Until')
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->height(40)
                    ->defaultImageUrl('/images/placeholder.png'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cta_text')
                    ->label('CTA'),
                TextColumn::make('sort_order')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->label('From')
                    ->placeholder('Always'),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->label('Until')
                    ->placeholder('Always'),
            ])
            ->defaultSort('sort_order')
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
            'index'  => Pages\ListFarmShopBanners::route('/'),
            'create' => Pages\CreateFarmShopBanner::route('/create'),
            'edit'   => Pages\EditFarmShopBanner::route('/{record}/edit'),
        ];
    }
}
