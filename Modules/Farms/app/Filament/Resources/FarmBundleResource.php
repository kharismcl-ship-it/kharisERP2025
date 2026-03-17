<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Resources\FarmBundleResource\Pages;
use Modules\Farms\Filament\Resources\FarmBundleResource\RelationManagers\BundleItemsRelationManager;
use Modules\Farms\Models\FarmBundle;

class FarmBundleResource extends Resource
{
    protected static ?string $model = FarmBundle::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Bundle Deals';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bundle Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('description')->rows(2)->columnSpanFull()->nullable(),
                    TextInput::make('discount_percentage')
                        ->label('Discount (%)')
                        ->numeric()
                        ->step(0.01)
                        ->default(0)
                        ->suffix('%')
                        ->helperText('e.g. 10 = 10% off each item in the bundle'),
                    TextInput::make('sort_order')->numeric()->default(0)->label('Sort Order'),
                    Toggle::make('is_active')->label('Active')->default(true)->columnSpanFull(),
                    FileUpload::make('images')
                        ->label('Bundle Images')
                        ->multiple()
                        ->image()
                        ->directory('bundle-images')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->limit(40),
                TextColumn::make('discount_percentage')->suffix('%')->label('Discount'),
                TextColumn::make('bundleItems_count')->counts('bundleItems')->label('Items'),
                BadgeColumn::make('is_active')->label('Status')
                    ->formatStateUsing(fn ($s) => $s ? 'Active' : 'Inactive')
                    ->colors(['success' => true, 'gray' => false]),
                TextColumn::make('sort_order')->label('Sort')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelationManagers(): array
    {
        return [BundleItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmBundles::route('/'),
            'create' => Pages\CreateFarmBundle::route('/create'),
            'view'   => Pages\ViewFarmBundle::route('/{record}'),
            'edit'   => Pages\EditFarmBundle::route('/{record}/edit'),
        ];
    }
}
