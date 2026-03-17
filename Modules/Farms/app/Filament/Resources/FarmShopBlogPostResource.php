<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmShopBlogPostResource\Pages;
use Modules\Farms\Models\FarmShopBlogPost;

class FarmShopBlogPostResource extends Resource
{
    protected static ?string $model = FarmShopBlogPost::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 14;

    protected static ?string $navigationLabel = 'Blog & Recipes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Content')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->helperText('URL-friendly identifier'),

                    Select::make('category')
                        ->options(['blog' => 'Blog Post', 'recipe' => 'Recipe'])
                        ->default('blog')
                        ->required(),

                    Textarea::make('excerpt')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->placeholder('Brief summary shown in post listings'),

                    RichEditor::make('content')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'heading', 'bulletList', 'orderedList',
                            'blockquote', 'link', 'attachFiles',
                        ])
                        ->columnSpanFull()
                        ->required(),
                ]),

            Section::make('Media')
                ->columns(2)
                ->schema([
                    FileUpload::make('cover_image_path')
                        ->label('Cover Image')
                        ->image()
                        ->directory('blog-covers')
                        ->columnSpanFull(),

                    TagsInput::make('tags')
                        ->placeholder('Add tag')
                        ->helperText('Press Enter after each tag'),

                    TextInput::make('reading_time_minutes')
                        ->label('Reading Time (minutes)')
                        ->numeric()
                        ->minValue(1)
                        ->default(2)
                        ->suffix('min'),
                ]),

            Section::make('Recipe (optional)')
                ->description('Only relevant for Recipe category posts')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TagsInput::make('ingredients')
                        ->placeholder('e.g. 2 tomatoes, 1 cup rice')
                        ->helperText('Add each ingredient and press Enter')
                        ->columnSpanFull(),
                ]),

            Section::make('Publishing')
                ->columns(2)
                ->schema([
                    Toggle::make('is_published')
                        ->label('Published')
                        ->helperText('Toggle to make visible on public shop'),

                    DateTimePicker::make('published_at')
                        ->label('Publish Date')
                        ->nullable()
                        ->placeholder('Immediate on save'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label('Cover')
                    ->width(60)->height(40)
                    ->defaultImageUrl(fn () => null)
                    ->toggleable(),

                TextColumn::make('title')->searchable()->limit(40),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn ($state) => $state === 'recipe' ? 'warning' : 'info')
                    ->formatStateUsing(fn ($state) => $state === 'recipe' ? 'Recipe' : 'Blog'),

                TextColumn::make('tags')
                    ->formatStateUsing(fn ($state) => $state ? implode(', ', $state) : '—')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('reading_time_minutes')
                    ->label('Read Time')
                    ->suffix(' min')
                    ->alignCenter(),

                TextColumn::make('is_published')
                    ->label('Published')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? 'Live' : 'Draft'),

                TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->options(['blog' => 'Blog Post', 'recipe' => 'Recipe']),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmShopBlogPosts::route('/'),
            'create' => Pages\CreateFarmShopBlogPost::route('/create'),
            'edit'   => Pages\EditFarmShopBlogPost::route('/{record}/edit'),
        ];
    }
}
