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
use Modules\Finance\Filament\Resources\FixedAssetResource\Pages;
use Modules\Finance\Models\FixedAsset;

class FixedAssetResource extends Resource
{
    protected static ?string $model = FixedAsset::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 49;

    protected static ?string $navigationLabel = 'Fixed Assets';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Asset Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('asset_code')
                            
                            ->maxLength(50)
                            ->placeholder('e.g. AST-0001')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('location')->maxLength(255),
                        Forms\Components\TextInput::make('serial_number')->maxLength(100),
                    ]),

                Section::make('Financial Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('acquisition_date')->required(),
                        Forms\Components\DatePicker::make('depreciation_start_date')->required(),
                        Forms\Components\TextInput::make('cost')
                            ->required()
                            ->numeric()
                            ->prefix('GHS'),
                        Forms\Components\TextInput::make('residual_value')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                        Forms\Components\TextInput::make('accumulated_depreciation')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0)
                            ->label('Accumulated Depreciation'),
                        Forms\Components\Select::make('status')
                            ->options(FixedAsset::STATUSES)
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->label('Category'),
                Tables\Columns\TextColumn::make('cost')->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('accumulated_depreciation')
                    ->money('GHS')
                    ->label('Accum. Deprec.'),
                Tables\Columns\TextColumn::make('acquisition_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'      => 'success',
                        'disposed'    => 'gray',
                        'written_off' => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(FixedAsset::STATUSES),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
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
            'index'  => Pages\ListFixedAssets::route('/'),
            'create' => Pages\CreateFixedAsset::route('/create'),
            'view'   => Pages\ViewFixedAsset::route('/{record}'),
            'edit'   => Pages\EditFixedAsset::route('/{record}/edit'),
        ];
    }
}
