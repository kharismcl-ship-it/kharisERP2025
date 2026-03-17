<?php

namespace Modules\HR\Filament\Resources\Staff\MyLoanResource\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\HR\Models\EmployeeLoan;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Loan Application')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('loan_type')
                        ->label('Loan Type')
                        ->options(EmployeeLoan::LOAN_TYPES)
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('principal_amount')
                        ->label('Amount Requested (GHS)')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->prefix('GHS')
                        ->live(),

                    Forms\Components\TextInput::make('repayment_months')
                        ->label('Repayment Period (months)')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->maxValue(60)
                        ->required()
                        ->live(),

                    Placeholder::make('estimated_monthly')
                        ->label('Est. Monthly Deduction')
                        ->content(function (Get $get): string {
                            $principal = (float) $get('principal_amount');
                            $months    = (int) $get('repayment_months');
                            if ($principal > 0 && $months > 0) {
                                return 'GHS ' . number_format($principal / $months, 2);
                            }
                            return '—';
                        }),

                    Forms\Components\Textarea::make('purpose')
                        ->label('Purpose / Reason')
                        ->required()
                        ->rows(4)
                        ->minLength(10)
                        ->columnSpanFull()
                        ->placeholder('Explain how you intend to use the loan and why you need it.'),
                ]),
        ]);
    }
}
