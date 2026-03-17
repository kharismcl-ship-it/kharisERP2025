<?php

namespace Modules\HR\Filament\Resources\Staff\MyPayslipResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\PayrollRun;

class PayslipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payslip Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('payrollRun.period_label')
                        ->label('Pay Period'),
                    TextEntry::make('payrollRun.status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'paid'      => 'success',
                            'finalized' => 'info',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => PayrollRun::STATUSES[$state] ?? ucfirst($state ?? '')),
                    TextEntry::make('payrollRun.payment_date')
                        ->label('Payment Date')
                        ->date()
                        ->placeholder('—'),
                    TextEntry::make('gross_salary')
                        ->label('Gross Salary')
                        ->numeric(2),
                    TextEntry::make('total_allowances')
                        ->label('Allowances')
                        ->numeric(2),
                    TextEntry::make('total_deductions')
                        ->label('Total Deductions')
                        ->numeric(2),
                    TextEntry::make('paye_tax')
                        ->label('PAYE Tax')
                        ->numeric(2),
                    TextEntry::make('ssnit_employee')
                        ->label('SSNIT (Employee)')
                        ->numeric(2),
                    TextEntry::make('net_salary')
                        ->label('Net Salary')
                        ->numeric(2),
                ]),
        ]);
    }
}
