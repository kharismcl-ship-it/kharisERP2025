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
use Modules\HR\Events\PayrollFinalized;
use Modules\HR\Filament\Resources\PayrollRunResource\Pages;
use Modules\HR\Filament\Resources\PayrollRunResource\RelationManagers\PayrollLinesRelationManager;
use Modules\HR\Models\PayrollRun;
use Modules\HR\Services\PayrollService;

class PayrollRunResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';
    protected static ?string $model = PayrollRun::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;


    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Payroll Runs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Period')
                    ->description('Define the payroll period and company')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ,
                        Forms\Components\Select::make('status')
                            ->options(PayrollRun::STATUSES)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('period_year')
                            ->options(array_combine(
                                range(date('Y') - 2, date('Y') + 1),
                                range(date('Y') - 2, date('Y') + 1)
                            ))
                            ->default(date('Y'))
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('period_month')
                            ->options(PayrollRun::MONTHS)
                            ->default(date('n'))
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('payment_date')
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),

                Section::make('Run Totals')
                    ->description('Auto-calculated when payroll lines are processed')
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('total_gross')
                            ->numeric()->prefix('GHS')->readOnly(),
                        Forms\Components\TextInput::make('total_deductions')
                            ->numeric()->prefix('GHS')->readOnly(),
                        Forms\Components\TextInput::make('total_net')
                            ->numeric()->prefix('GHS')->readOnly(),
                        Forms\Components\TextInput::make('total_paye')
                            ->label('PAYE Tax')
                            ->numeric()->prefix('GHS')->readOnly(),
                        Forms\Components\TextInput::make('total_ssnit')
                            ->label('SSNIT (Employee)')
                            ->numeric()->prefix('GHS')->readOnly(),
                        Forms\Components\TextInput::make('employee_count')
                            ->label('No. of Employees')
                            ->numeric()->readOnly(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period_year', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('period_label')
                    ->label('Period')
                    ->getStateUsing(fn (PayrollRun $record) => $record->period_label)
                    ->weight('bold')
                    ->searchable(['period_year', 'period_month'])
                    ->sortable(['period_year', 'period_month']),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('employee_count')
                    ->label('Employees')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_gross')
                    ->label('Gross')
                    ->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('total_deductions')
                    ->label('Deductions')
                    ->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('total_net')
                    ->label('Net Pay')
                    ->money('GHS')->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'      => 'gray',
                        'processing' => 'warning',
                        'finalized'  => 'success',
                        'paid'       => 'info',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(PayrollRun::STATUSES),
                Tables\Filters\SelectFilter::make('period_year')
                    ->options(array_combine(
                        range(date('Y') - 2, date('Y') + 1),
                        range(date('Y') - 2, date('Y') + 1)
                    ))
                    ->label('Year'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('process')
                        ->label('Process Payroll')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Process Payroll')
                        ->modalDescription('This will calculate payroll lines for all active employees in this period. Existing lines will be replaced. Continue?')
                        ->visible(fn (PayrollRun $record) => $record->status === 'draft')
                        ->action(function (PayrollRun $record) {
                            try {
                                $run = app(PayrollService::class)->processExistingRun($record);
                                Notification::make()
                                    ->title('Payroll processed successfully')
                                    ->body("{$run->employee_count} employee lines generated.")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Payroll processing failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('finalize')
                        ->label('Finalize')
                        ->icon('heroicon-o-lock-closed')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Finalize Payroll Run')
                        ->modalDescription('Once finalized, payroll lines cannot be edited. Proceed?')
                        ->visible(fn (PayrollRun $record) => $record->status === 'processing')
                        ->action(function (PayrollRun $record) {
                            $record->update([
                                'status'       => 'finalized',
                                'finalized_at' => now(),
                                'finalized_by' => auth()->id(),
                            ]);
                            Notification::make()->title('Payroll finalized')->success()->send();
                        }),
                    Action::make('markPaid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Payroll as Paid')
                        ->modalDescription('Confirm that net salaries have been disbursed to all employees.')
                        ->visible(fn (PayrollRun $record) => $record->status === 'finalized')
                        ->action(function (PayrollRun $record) {
                            $record->update(['status' => 'paid']);
                            event(new PayrollFinalized($record));
                            Notification::make()->title('Payroll marked as paid')->success()->send();
                        }),
                    Action::make('postToFinance')
                        ->label('Post to Finance')
                        ->icon('heroicon-o-book-open')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Post Payroll to Finance')
                        ->modalDescription('This will create a journal entry in the Finance module for this payroll run. The accounts 5210, 5220, 2120, 2140, and 2150 must exist.')
                        ->visible(fn (PayrollRun $record) => in_array($record->status, ['finalized', 'paid']))
                        ->action(function (PayrollRun $record) {
                            try {
                                app(PayrollService::class)->postToFinance($record);
                                Notification::make()
                                    ->title('Payroll posted to Finance')
                                    ->body('Journal entry created successfully.')
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Finance posting failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PayrollLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'view'   => Pages\ViewPayrollRun::route('/{record}'),
            'edit'   => Pages\EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
