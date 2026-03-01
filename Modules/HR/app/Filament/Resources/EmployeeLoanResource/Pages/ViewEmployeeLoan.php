<?php

namespace Modules\HR\Filament\Resources\EmployeeLoanResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\EmployeeLoanResource;
use Modules\HR\Models\EmployeeLoan;

class ViewEmployeeLoan extends ViewRecord
{
    protected static string $resource = EmployeeLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('approve')
                ->label('Approve Loan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->update([
                        'status'              => 'approved',
                        'approved_date'       => now(),
                        'outstanding_balance' => $this->record->principal_amount,
                        'approved_by'         => auth()->id(),
                    ]);
                    $this->refreshFormData(['status', 'approved_date', 'outstanding_balance']);
                    Notification::make()->title('Loan approved')->success()->send();
                }),
            Action::make('activate')
                ->label('Activate Loan')
                ->icon('heroicon-o-play')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'approved')
                ->action(function () {
                    $this->record->update(['status' => 'active', 'start_date' => now()]);
                    $this->refreshFormData(['status', 'start_date']);
                    Notification::make()->title('Loan activated')->send();
                }),
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->update(['status' => 'rejected']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Loan rejected')->danger()->send();
                }),
            Action::make('markCleared')
                ->label('Mark Cleared')
                ->icon('heroicon-o-check-badge')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'active')
                ->action(function () {
                    $this->record->update(['status' => 'cleared', 'outstanding_balance' => 0]);
                    $this->refreshFormData(['status', 'outstanding_balance']);
                    Notification::make()->title('Loan cleared')->success()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Loan Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('employee.full_name')
                            ->label('Employee')
                            ->getStateUsing(fn (EmployeeLoan $record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                            ->weight('bold'),
                        TextEntry::make('loan_type')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn ($state) => EmployeeLoan::LOAN_TYPES[$state] ?? ucfirst($state)),
                        TextEntry::make('status')
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
                        TextEntry::make('principal_amount')->label('Loan Amount')->money('GHS'),
                        TextEntry::make('outstanding_balance')->label('Outstanding Balance')->money('GHS')
                            ->color(fn (EmployeeLoan $record): string => $record->outstanding_balance > 0 ? 'warning' : 'success'),
                        TextEntry::make('monthly_deduction')->label('Monthly Deduction')->money('GHS')->placeholder('—'),
                        TextEntry::make('repayment_months')->label('Repayment Period')->suffix(' months')->placeholder('—'),
                        TextEntry::make('company.name')->label('Company'),
                    ]),

                Section::make('Dates')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('approved_date')->label('Approved On')->date()->placeholder('—'),
                        TextEntry::make('start_date')->label('Started On')->date()->placeholder('—'),
                        TextEntry::make('expected_end_date')->label('Expected End')->date()->placeholder('—'),
                    ]),

                Section::make('Purpose & Notes')
                    ->schema([
                        TextEntry::make('purpose')->columnSpanFull()->placeholder('—'),
                        TextEntry::make('rejection_reason')->columnSpanFull()->placeholder('—')
                            ->visible(fn (EmployeeLoan $record) => $record->status === 'rejected'),
                    ]),
            ]);
    }
}
