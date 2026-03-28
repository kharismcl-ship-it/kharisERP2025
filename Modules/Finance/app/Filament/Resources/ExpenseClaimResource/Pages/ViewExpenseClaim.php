<?php

namespace Modules\Finance\Filament\Resources\ExpenseClaimResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\ExpenseClaimResource;

class ViewExpenseClaim extends ViewRecord
{
    protected static string $resource = ExpenseClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Claim Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('claim_number')->weight('bold'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'     => 'gray',
                                'submitted' => 'warning',
                                'approved'  => 'success',
                                'rejected'  => 'danger',
                                'paid'      => 'info',
                                default     => 'gray',
                            }),
                        TextEntry::make('employee.full_name')->label('Employee')->placeholder('—'),
                        TextEntry::make('claim_date')->date(),
                        TextEntry::make('total')->money('GHS')->weight('bold'),
                        TextEntry::make('submitted_at')->dateTime()->placeholder('—'),
                    ]),

                Section::make('Purpose & Notes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('purpose')->columnSpanFull(),
                        TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('rejection_reason')->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }
}