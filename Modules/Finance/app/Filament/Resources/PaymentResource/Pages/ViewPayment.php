<?php

namespace Modules\Finance\Filament\Resources\PaymentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\PaymentResource;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice.invoice_number')
                            ->label('Invoice')
                            ->weight('bold')
                            ->placeholder('—'),
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('amount')->money('GHS')->label('Amount'),
                        TextEntry::make('payment_date')->date()->label('Payment Date'),
                        TextEntry::make('payment_method')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'cash'   => 'success',
                                'bank'   => 'info',
                                'momo'   => 'warning',
                                'card'   => 'primary',
                                'cheque' => 'gray',
                                default  => 'gray',
                            }),
                        TextEntry::make('reference')->placeholder('—'),
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
