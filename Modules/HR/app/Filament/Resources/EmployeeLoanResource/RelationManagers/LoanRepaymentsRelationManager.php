<?php

namespace Modules\HR\Filament\Resources\EmployeeLoanResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LoanRepaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'repayments';

    protected static ?string $title = 'Repayment History';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('payment_date')->required()->native(false),
                Forms\Components\TextInput::make('amount')->numeric()->prefix('GHS')->required(),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'payroll_deduction' => 'Payroll Deduction',
                        'bank_transfer'     => 'Bank Transfer',
                        'cash'              => 'Cash',
                        'cheque'            => 'Cheque',
                    ])
                    ->required()->native(false),
                Forms\Components\TextInput::make('reference_number')
                    ->label('Reference No.')->nullable(),
                Forms\Components\Textarea::make('notes')->columnSpanFull()->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')->date()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('amount')->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'payroll_deduction' => 'info',
                        'bank_transfer'     => 'primary',
                        'cash'              => 'success',
                        'cheque'            => 'warning',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('reference_number')->label('Reference')->placeholder('—'),
                Tables\Columns\TextColumn::make('notes')->limit(50)->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
