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
use Modules\Finance\Filament\Resources\TaxRateResource\Pages;
use Modules\Finance\Models\TaxRate;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 45;

    protected static ?string $navigationLabel = 'Tax Rates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Tax Rate Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('e.g. VAT15')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Standard VAT'),
                        Forms\Components\TextInput::make('rate')
                            ->required()
                            ->numeric()
                            ->step(0.0001)
                            ->suffix('%')
                            ->placeholder('e.g. 15.0000'),
                        Forms\Components\Select::make('type')
                            ->options(TaxRate::TYPES)
                            ->required(),
                        Forms\Components\Select::make('applies_to')
                            ->options(TaxRate::APPLIES_TO)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            ->searchable()
                            ->placeholder('System-wide (all companies)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => TaxRate::TYPES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'vat'         => 'info',
                        'nhil'        => 'warning',
                        'getf'        => 'primary',
                        'withholding' => 'danger',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('applies_to')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('company.name')
                    ->placeholder('System-wide')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(TaxRate::TYPES),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'view'   => Pages\ViewTaxRate::route('/{record}'),
            'edit'   => Pages\EditTaxRate::route('/{record}/edit'),
        ];
    }
}
