<?php

namespace Modules\HR\Filament\Resources\PayrollRunResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\PayrollRunResource;
use Modules\HR\Models\PayrollRun;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Period')
                    ->collapsible()
                    ->columns(['default' => 2, 'xl' => 3])
                    ->schema([
                        TextEntry::make('period_label')
                            ->label('Period')
                            ->getStateUsing(fn (PayrollRun $record) => $record->period_label)
                            ->weight('bold'),
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft'      => 'gray',
                                'processing' => 'warning',
                                'finalized'  => 'success',
                                'paid'       => 'info',
                                default      => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('payment_date')->date()->placeholder('Not set'),
                        TextEntry::make('finalized_at')->dateTime()->placeholder('Not finalized'),
                        TextEntry::make('finalizer.name')->label('Finalized By')->placeholder('—'),
                    ]),

                Section::make('Payroll Totals')
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('employee_count')
                            ->label('Employees')
                            ->numeric(),
                        TextEntry::make('total_gross')
                            ->label('Total Gross')
                            ->money('GHS'),
                        TextEntry::make('total_deductions')
                            ->label('Total Deductions')
                            ->money('GHS'),
                        TextEntry::make('total_net')
                            ->label('Net Pay')
                            ->money('GHS')
                            ->weight('bold'),
                    ]),

                Section::make('Notes')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('notes')->columnSpanFull()->placeholder('No notes'),
                    ]),

                Section::make('Audit')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('creator.name')->label('Created By')->placeholder('—'),
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('process')
                ->label('Process Payroll')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $this->record->update(['status' => 'processing']);
                    Notification::make()->title('Payroll is being processed')->warning()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('finalize')
                ->label('Finalize')
                ->icon('heroicon-o-lock-closed')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('Once finalized, payroll lines cannot be edited. Proceed?')
                ->visible(fn () => $this->record->status === 'processing')
                ->action(function () {
                    $this->record->update([
                        'status'       => 'finalized',
                        'finalized_at' => now(),
                        'finalized_by' => auth()->id(),
                    ]);
                    Notification::make()->title('Payroll finalized')->success()->send();
                    $this->refreshFormData(['status', 'finalized_at', 'finalized_by']);
                }),
            Action::make('markPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'finalized')
                ->action(function () {
                    $this->record->update(['status' => 'paid']);
                    Notification::make()->title('Payroll marked as paid')->success()->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
