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
use Modules\Finance\Filament\Resources\BankAccountResource\Pages;
use Modules\Finance\Models\BankAccount;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 47;

    protected static ?string $navigationLabel = 'Bank Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            
                            ->maxLength(255)
                            ->placeholder('e.g. Main Operating Account'),
                        Forms\Components\TextInput::make('bank_name')
                            
                            ->maxLength(255)
                            ->placeholder('e.g. GCB Bank'),
                        Forms\Components\TextInput::make('account_number')
                            
                            ->maxLength(50),
                        Forms\Components\TextInput::make('branch')
                            ->maxLength(255)
                            ->placeholder('e.g. Accra Main'),
                        Forms\Components\TextInput::make('currency')
                            ->default('GHS')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('opening_balance')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                        Forms\Components\Select::make('gl_account_id')
                            ->label('GL Account (Bank)')
                            ->relationship('glAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Link to chart of accounts'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
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
                Tables\Columns\TextColumn::make('bank_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('account_number'),
                Tables\Columns\TextColumn::make('currency')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('opening_balance')
                    ->money('GHS')
                    ->label('Opening Balance'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index'  => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'view'   => Pages\ViewBankAccount::route('/{record}'),
            'edit'   => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
