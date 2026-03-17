<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmShopPageResource\Pages;
use Modules\Farms\Models\FarmShopPage;

class FarmShopPageResource extends Resource
{
    protected static ?string $model = FarmShopPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?string $navigationLabel = 'Shop Pages';

    protected static ?int $navigationSort = 37;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page Details')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(200)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('slug', str($state)->slug()->toString());
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(100)
                        ->helperText('URL: /farm-shop/pages/{slug}')
                        ->rules(['alpha_dash']),
                    Toggle::make('is_published')
                        ->default(true)
                        ->columnSpanFull(),
                ]),
            Section::make('Content')
                ->schema([
                    RichEditor::make('content')
                        ->label('')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'bulletList', 'orderedList',
                            'blockquote', 'link',
                        ]),
                ]),
            Section::make('SEO')
                ->collapsed()
                ->columns(1)
                ->schema([
                    TextInput::make('meta_title')->maxLength(200),
                    Textarea::make('meta_description')->rows(2)->maxLength(500),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('slug')
                    ->prefix('/farm-shop/pages/')
                    ->copyable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
                TextColumn::make('updated_at')->dateTime()->label('Updated'),
            ])
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
            'index'  => Pages\ListFarmShopPages::route('/'),
            'create' => Pages\CreateFarmShopPage::route('/create'),
            'edit'   => Pages\EditFarmShopPage::route('/{record}/edit'),
        ];
    }
}
