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
use Modules\Finance\Filament\Resources\AccountResource\Pages;
use Modules\Finance\Models\Account;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 40;

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
                        Forms\Components\TextInput::make('code')
                            
                            ->maxLength(50)
                            ->placeholder('e.g. 1100'),
                        Forms\Components\TextInput::make('name')
                            
                            ->maxLength(255)
                            ->placeholder('e.g. Accounts Receivable'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'asset'     => 'Asset',
                                'liability' => 'Liability',
                                'equity'    => 'Equity',
                                'income'    => 'Income',
                                'expense'   => 'Expense',
                            ])
                            ->required(),
                    ]),

                Section::make('Chart Hierarchy')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Account')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (top-level account)'),
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
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'asset'     => 'info',
                        'liability' => 'warning',
                        'equity'    => 'success',
                        'income'    => 'success',
                        'expense'   => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Account')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'asset'     => 'Asset',
                        'liability' => 'Liability',
                        'equity'    => 'Equity',
                        'income'    => 'Income',
                        'expense'   => 'Expense',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view'   => Pages\ViewAccount::route('/{record}'),
            'edit'   => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
