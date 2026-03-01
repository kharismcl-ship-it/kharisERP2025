<?php

namespace Modules\Finance\Filament\Resources\RecurringInvoiceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\RecurringInvoiceResource;
use Modules\Finance\Models\RecurringInvoice;

class ViewRecurringInvoice extends ViewRecord
{
    protected static string $resource = RecurringInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('customer_name')->weight('bold'),
                        TextEntry::make('customer_email')->placeholder('—'),
                        TextEntry::make('company.name')->label('Company'),
                    ]),

                Section::make('Invoice Template')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('description')->columnSpanFull()->placeholder('None'),
                        TextEntry::make('amount')->money('GHS'),
                        TextEntry::make('tax_total')->money('GHS')->label('Tax Total'),
                    ]),

                Section::make('Schedule')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('frequency')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => RecurringInvoice::FREQUENCIES[$state] ?? $state)
                            ->color('info'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => RecurringInvoice::STATUSES[$state] ?? $state)
                            ->color(fn (string $state) => match ($state) {
                                'active'    => 'success',
                                'paused'    => 'warning',
                                'completed' => 'gray',
                                'cancelled' => 'danger',
                                default     => 'gray',
                            }),
                        TextEntry::make('start_date')->date(),
                        TextEntry::make('end_date')->date()->placeholder('Indefinite'),
                        TextEntry::make('next_run_date')->date()->label('Next Invoice Date'),
                        TextEntry::make('last_run_date')->date()->placeholder('Not yet run'),
                        TextEntry::make('invoices_generated')->label('Invoices Generated'),
                    ]),

                Section::make('Audit')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->label('Created'),
                        TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                    ]),
            ]);
    }
}
