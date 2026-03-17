<?php

namespace Modules\HR\Filament\Resources\Staff\MyLoanResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\EmployeeLoan;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => EmployeeLoan::LOAN_TYPES[$state] ?? ucfirst(str_replace('_', ' ', $state)))
                    ->badge()->color('gray'),
                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Principal')
                    ->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->label('Balance')
                    ->money('GHS')
                    ->color(fn ($state) => (float) $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('monthly_deduction')
                    ->label('Monthly Deduction')
                    ->money('GHS'),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('expected_end_date')
                    ->label('Expected End')
                    ->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'warning',
                        'cleared'   => 'success',
                        'approved'  => 'info',
                        'pending'   => 'gray',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeLoan::STATUSES[$state] ?? ucfirst($state)),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (EmployeeLoan $r) => $r->status === 'pending'),
            ])
            ->bulkActions([]);
    }
}
