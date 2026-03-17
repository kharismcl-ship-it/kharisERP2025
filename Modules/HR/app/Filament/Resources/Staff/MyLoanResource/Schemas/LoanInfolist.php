<?php

namespace Modules\HR\Filament\Resources\Staff\MyLoanResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\EmployeeLoan;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Loan Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('loan_type')
                        ->label('Loan Type')
                        ->formatStateUsing(fn ($state) => EmployeeLoan::LOAN_TYPES[$state] ?? ucfirst(str_replace('_', ' ', $state ?? ''))),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'   => 'warning',
                            'cleared'  => 'success',
                            'approved' => 'info',
                            'pending'  => 'gray',
                            'rejected' => 'danger',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => EmployeeLoan::STATUSES[$state] ?? ucfirst($state ?? '')),
                    TextEntry::make('principal_amount')
                        ->label('Principal')
                        ->numeric(2),
                    TextEntry::make('outstanding_balance')
                        ->numeric(2),
                    TextEntry::make('monthly_deduction')
                        ->numeric(2),
                    TextEntry::make('approved_date')
                        ->date()
                        ->placeholder('—'),
                    TextEntry::make('start_date')
                        ->date()
                        ->placeholder('—'),
                    TextEntry::make('expected_end_date')
                        ->label('Expected End')
                        ->date()
                        ->placeholder('—'),
                    TextEntry::make('purpose')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
