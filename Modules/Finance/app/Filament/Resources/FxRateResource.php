<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\FxRateResource\Pages;
use Modules\Finance\Models\Currency;
use Modules\Finance\Models\FxRate;

class FxRateResource extends Resource
{
    protected static ?string $model = FxRate::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 44;

    protected static ?string $navigationLabel = 'FX Rates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('from_currency')
                            ->options(fn () => Currency::where('is_active', true)->pluck('code', 'code'))
                            ->required()
                            ->searchable()
                            ->label('From Currency'),
                        Forms\Components\Select::make('to_currency')
                            ->options(fn () => Currency::where('is_active', true)->pluck('code', 'code'))
                            ->required()
                            ->searchable()
                            ->label('To Currency'),
                        Forms\Components\TextInput::make('rate')
                            ->numeric()
                            ->required()
                            ->step('0.000001')
                            ->label('Exchange Rate'),
                        Forms\Components\DatePicker::make('effective_date')
                            ->required()
                            ->default(now()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_currency')->sortable(),
                Tables\Columns\TextColumn::make('to_currency')->sortable(),
                Tables\Columns\TextColumn::make('rate')->sortable(),
                Tables\Columns\TextColumn::make('effective_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFxRates::route('/'),
            'create' => Pages\CreateFxRate::route('/create'),
            'edit'   => Pages\EditFxRate::route('/{record}/edit'),
        ];
    }
}