<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\BankReconciliationResource\Pages;
use Modules\Finance\Models\BankReconciliation;

class BankReconciliationResource extends Resource
{
    protected static ?string $model = BankReconciliation::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 48;

    protected static ?string $navigationLabel = 'Bank Reconciliation';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Reconciliation Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('bank_account_id')
                            ->relationship('bankAccount', 'name')
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('statement_date')->required(),
                        Forms\Components\TextInput::make('statement_balance')
                            ->required()
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Bank Statement Balance'),
                        Forms\Components\TextInput::make('book_balance')
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Book Balance (GL)')
                            ->default(0),
                        Forms\Components\TextInput::make('difference')
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Difference')
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->options(BankReconciliation::STATUSES)
                            ->default('draft')
                            ->required(),
                    ]),

                Section::make('Notes')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Any outstanding items, timing differences, etc.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bankAccount.name')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('statement_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('statement_balance')->money('GHS'),
                Tables\Columns\TextColumn::make('book_balance')->money('GHS'),
                Tables\Columns\TextColumn::make('difference')
                    ->money('GHS')
                    ->color(fn ($state) => (float) $state === 0.0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'reconciled' => 'success',
                        'draft'      => 'warning',
                        default      => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BankReconciliation::STATUSES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('mark_reconciled')
                        ->label('Mark Reconciled')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (BankReconciliation $record) => $record->status === 'draft')
                        ->action(function (BankReconciliation $record) {
                            $record->complete(auth()->id());
                            Notification::make()->title('Reconciliation completed.')->success()->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('statement_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBankReconciliations::route('/'),
            'create' => Pages\CreateBankReconciliation::route('/create'),
            'view'   => Pages\ViewBankReconciliation::route('/{record}'),
            'edit'   => Pages\EditBankReconciliation::route('/{record}/edit'),
        ];
    }
}
