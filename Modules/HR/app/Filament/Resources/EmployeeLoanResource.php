<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
use Modules\HR\Filament\Resources\EmployeeLoanResource\Pages;
use Modules\HR\Filament\Resources\EmployeeLoanResource\RelationManagers\LoanRepaymentsRelationManager;
use Modules\HR\Models\EmployeeLoan;

class EmployeeLoanResource extends Resource
{
    protected static ?string $model = EmployeeLoan::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|\UnitEnum|null $navigationGroup = 'Benefits & Loans';

    protected static ?int $navigationSort = 67;

    protected static ?string $navigationLabel = 'Employee Loans';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Loan Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('loan_type')
                            ->options(EmployeeLoan::LOAN_TYPES)
                            ->required()->native(false),
                        Forms\Components\Select::make('status')
                            ->options(EmployeeLoan::STATUSES)
                            ->required()->native(false),
                        Forms\Components\TextInput::make('principal_amount')
                            ->label('Loan Amount')->numeric()->prefix('GHS')->required(),
                        Forms\Components\TextInput::make('outstanding_balance')
                            ->numeric()->prefix('GHS')->readOnly(),
                        Forms\Components\TextInput::make('monthly_deduction')
                            ->label('Monthly Deduction')->numeric()->prefix('GHS')->nullable(),
                        Forms\Components\TextInput::make('repayment_months')
                            ->label('Repayment Period (months)')->numeric()->nullable(),
                        Forms\Components\DatePicker::make('approved_date')->native(false)->nullable(),
                        Forms\Components\DatePicker::make('start_date')->native(false)->nullable(),
                        Forms\Components\DatePicker::make('expected_end_date')->native(false)->nullable(),
                    ]),

                Section::make('Purpose & Notes')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('purpose')->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->columnSpanFull()
                            ->visible(fn ($get) => $get('status') === 'rejected'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->getStateUsing(fn ($r) => $r->employee->first_name . ' ' . $r->employee->last_name)
                    ->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('loan_type')
                    ->badge()->color('primary')
                    ->formatStateUsing(fn ($state) => EmployeeLoan::LOAN_TYPES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'gray',
                        'approved' => 'info',
                        'active'   => 'success',
                        'cleared'  => 'gray',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Amount')->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->label('Outstanding')->money('GHS')->sortable()
                    ->color(fn (EmployeeLoan $r): string => $r->outstanding_balance > 0 ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('monthly_deduction')
                    ->label('Monthly')->money('GHS')->placeholder('—'),
                Tables\Columns\TextColumn::make('expected_end_date')
                    ->label('Due Date')->date()->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')->options(EmployeeLoan::STATUSES),
                Tables\Filters\SelectFilter::make('loan_type')->options(EmployeeLoan::LOAN_TYPES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('approve')
                        ->label('Approve Loan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Loan Application')
                        ->visible(fn (EmployeeLoan $r) => $r->status === 'pending')
                        ->action(function (EmployeeLoan $record) {
                            $record->update([
                                'status'          => 'approved',
                                'approved_date'   => now(),
                                'outstanding_balance' => $record->principal_amount,
                                'approved_by'     => auth()->id(),
                            ]);
                            Notification::make()->title('Loan approved')->success()->send();
                        }),
                    Action::make('activate')
                        ->label('Activate Loan')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn (EmployeeLoan $r) => $r->status === 'approved')
                        ->action(function (EmployeeLoan $record) {
                            $record->update(['status' => 'active', 'start_date' => now()]);
                            Notification::make()->title('Loan activated')->send();
                        }),
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (EmployeeLoan $r) => $r->status === 'pending')
                        ->action(function (EmployeeLoan $record) {
                            $record->update(['status' => 'rejected']);
                            Notification::make()->title('Loan rejected')->danger()->send();
                        }),
                    Action::make('markCleared')
                        ->label('Mark Cleared')
                        ->icon('heroicon-o-check-badge')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn (EmployeeLoan $r) => $r->status === 'active')
                        ->action(function (EmployeeLoan $record) {
                            $record->update(['status' => 'cleared', 'outstanding_balance' => 0]);
                            Notification::make()->title('Loan cleared')->success()->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            LoanRepaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployeeLoans::route('/'),
            'create' => Pages\CreateEmployeeLoan::route('/create'),
            'view'   => Pages\ViewEmployeeLoan::route('/{record}'),
            'edit'   => Pages\EditEmployeeLoan::route('/{record}/edit'),
        ];
    }
}