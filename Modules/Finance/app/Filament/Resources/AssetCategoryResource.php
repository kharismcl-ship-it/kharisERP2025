<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\AssetCategoryResource\Pages;
use Modules\Finance\Models\AssetCategory;

class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Asset Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Category Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            ->placeholder('System-wide')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Motor Vehicles'),
                        Forms\Components\Select::make('depreciation_method')
                            ->options(AssetCategory::DEPRECIATION_METHODS)
                            ->default('straight_line')
                            ->required(),
                        Forms\Components\TextInput::make('useful_life_years')
                            ->numeric()
                            ->default(5)
                            ->suffix('years'),
                        Forms\Components\TextInput::make('residual_rate')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->label('Residual Value Rate'),
                    ]),

                Section::make('GL Accounts')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('asset_account_id')
                            ->label('Asset Account')
                            ->relationship('assetAccount', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('depreciation_account_id')
                            ->label('Depreciation Expense Account')
                            ->relationship('depreciationAccount', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('accumulated_depreciation_account_id')
                            ->label('Accumulated Depreciation Account')
                            ->relationship('accumulatedDepreciationAccount', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('depreciation_method')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => AssetCategory::DEPRECIATION_METHODS[$state] ?? $state)
                    ->color('info'),
                Tables\Columns\TextColumn::make('useful_life_years')
                    ->suffix(' yrs')
                    ->label('Useful Life'),
                Tables\Columns\TextColumn::make('residual_rate')
                    ->suffix('%')
                    ->label('Residual %'),
                Tables\Columns\TextColumn::make('company.name')
                    ->placeholder('System-wide')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAssetCategories::route('/'),
            'create' => Pages\CreateAssetCategory::route('/create'),
            'view'   => Pages\ViewAssetCategory::route('/{record}'),
            'edit'   => Pages\EditAssetCategory::route('/{record}/edit'),
        ];
    }
}
