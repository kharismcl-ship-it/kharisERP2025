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
use Modules\Finance\Filament\Resources\PettyCashResource\Pages;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\PettyCashFund;
use Modules\Finance\Models\PettyCashTransaction;

class PettyCashResource extends Resource
{
    protected static ?string $model = PettyCashFund::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 49;

    protected static ?string $navigationLabel = 'Petty Cash';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('custodian_employee_id')
                            ->relationship('custodian', 'full_name')
                            ->searchable()
                            ->label('Custodian'),
                        Forms\Components\TextInput::make('float_amount')
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Float Amount')
                            ->default(0),
                        Forms\Components\TextInput::make('current_balance')
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Current Balance')
                            ->default(0),
                        Forms\Components\Select::make('gl_account_id')
                            ->relationship('glAccount', 'name')
                            ->searchable()
                            ->label('GL Account'),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('custodian.full_name')
                    ->label('Custodian')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('float_amount')
                    ->money('GHS')
                    ->label('Float'),
                Tables\Columns\TextColumn::make('current_balance')
                    ->money('GHS')
                    ->label('Balance')
                    ->color(fn (PettyCashFund $record) => $record->current_balance >= 0 ? 'success' : 'danger'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('add_transaction')
                    ->label('Add Transaction')
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('transaction_type')
                            ->options([
                                'expense'       => 'Expense',
                                'replenishment' => 'Replenishment',
                                'adjustment'    => 'Adjustment',
                            ])
                            ->required()
                            ->default('expense'),
                        Forms\Components\TextInput::make('description')->required(),
                        Forms\Components\TextInput::make('amount')->numeric()->prefix('GHS')->required(),
                        Forms\Components\Select::make('expense_category_id')
                            ->label('Category')
                            ->options(fn () => ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\DatePicker::make('transaction_date')->required()->default(now()),
                        Forms\Components\FileUpload::make('receipt_path')
                            ->label('Receipt')
                            ->disk('public')
                            ->directory('finance/petty-cash'),
                    ])
                    ->action(function (PettyCashFund $record, array $data) {
                        $amount = (float) $data['amount'];
                        if ($data['transaction_type'] === 'expense') {
                            $newBalance = $record->current_balance - $amount;
                        } else {
                            $newBalance = $record->current_balance + $amount;
                        }

                        PettyCashTransaction::create([
                            'fund_id'             => $record->id,
                            'transaction_type'    => $data['transaction_type'],
                            'description'         => $data['description'],
                            'amount'              => $amount,
                            'balance_after'       => $newBalance,
                            'expense_category_id' => $data['expense_category_id'] ?? null,
                            'receipt_path'        => $data['receipt_path'] ?? null,
                            'transaction_date'    => $data['transaction_date'],
                            'recorded_by_user_id' => auth()->id(),
                        ]);

                        $record->update(['current_balance' => $newBalance]);
                    }),
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
            'index'  => Pages\ListPettyCashFunds::route('/'),
            'create' => Pages\CreatePettyCashFund::route('/create'),
            'edit'   => Pages\EditPettyCashFund::route('/{record}/edit'),
        ];
    }
}