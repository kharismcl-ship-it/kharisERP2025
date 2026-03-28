<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\Action;
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
use Modules\Finance\Filament\Resources\ExpenseClaimResource\Pages;
use Modules\Finance\Models\ExpenseClaim;
use Modules\Finance\Models\ExpenseCategory;

class ExpenseClaimResource extends Resource
{
    protected static ?string $model = ExpenseClaim::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 18;

    protected static ?string $navigationLabel = 'Expense Claims';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Claim Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'full_name')
                            ->searchable()
                            ->label('Employee'),
                        Forms\Components\TextInput::make('claim_number')
                            ->disabled()
                            ->placeholder('Auto-generated'),
                        Forms\Components\DatePicker::make('claim_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'submitted' => 'Submitted',
                                'approved'  => 'Approved',
                                'rejected'  => 'Rejected',
                                'paid'      => 'Paid',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Textarea::make('purpose')
                            ->required()
                            ->columnSpanFull()
                            ->rows(2),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),

                Section::make('Expense Lines')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->relationship('lines')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('expense_category_id')
                                    ->label('Category')
                                    ->options(fn () => ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable(),
                                Forms\Components\TextInput::make('description')->required(),
                                Forms\Components\DatePicker::make('expense_date')->required(),
                                Forms\Components\TextInput::make('amount')->numeric()->prefix('GHS')->required(),
                                Forms\Components\FileUpload::make('receipt_path')
                                    ->label('Receipt')
                                    ->disk('public')
                                    ->directory('finance/expense-receipts')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('claim_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('claim_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'paid'      => 'info',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Submitted',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'paid'      => 'Paid',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('warning')
                    ->visible(fn (ExpenseClaim $record) => $record->status === 'draft')
                    ->action(fn (ExpenseClaim $record) => $record->update(['status' => 'submitted', 'submitted_at' => now()])),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (ExpenseClaim $record) => $record->status === 'submitted')
                    ->action(fn (ExpenseClaim $record) => $record->update([
                        'status'              => 'approved',
                        'approved_by_user_id' => auth()->id(),
                        'approved_at'         => now(),
                    ])),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (ExpenseClaim $record) => $record->status === 'submitted')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->label('Rejection Reason')
                            ->rows(3),
                    ])
                    ->action(fn (ExpenseClaim $record, array $data) => $record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ])),
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
            'index'  => Pages\ListExpenseClaims::route('/'),
            'create' => Pages\CreateExpenseClaim::route('/create'),
            'view'   => Pages\ViewExpenseClaim::route('/{record}'),
            'edit'   => Pages\EditExpenseClaim::route('/{record}/edit'),
        ];
    }
}