<?php

namespace Modules\HR\Filament\Resources\Staff\MyPayslipResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class PayslipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('payrollRun.name')
                    ->label('Pay Period')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('payrollRun.period_start')
                    ->label('From')
                    ->date(),
                Tables\Columns\TextColumn::make('payrollRun.period_end')
                    ->label('To')
                    ->date(),
                Tables\Columns\TextColumn::make('gross_salary')
                    ->label('Gross Pay')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_deductions')
                    ->label('Deductions')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('net_pay')
                    ->label('Net Pay')
                    ->money('USD')
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('payrollRun.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'      => 'success',
                        'finalized' => 'info',
                        default     => 'gray',
                    }),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
