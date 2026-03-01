<?php

namespace Modules\Finance\Filament\Resources\ReceiptResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\ReceiptResource;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('customer_name')->label('Customer')->weight('bold'),
                        TextEntry::make('customer_type')->label('Customer Type')->placeholder('—'),
                        TextEntry::make('customer_email')->label('Email')->placeholder('—'),
                        TextEntry::make('customer_phone')->label('Phone')->placeholder('—'),
                    ]),

                Section::make('Receipt Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('receipt_number')->weight('bold'),
                        TextEntry::make('receipt_date')->date()->label('Receipt Date'),
                        TextEntry::make('amount')->money('GHS')->label('Amount'),
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
                        TextEntry::make('invoice.invoice_number')->label('Invoice')->placeholder('—'),
                        TextEntry::make('reference')->placeholder('—'),
                        TextEntry::make('company.name')->label('Company'),
                    ]),

                Section::make('Status Tracking')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'      => 'gray',
                                'sent'       => 'info',
                                'viewed'     => 'warning',
                                'downloaded' => 'success',
                                default      => 'gray',
                            }),
                        TextEntry::make('sent_at')->dateTime()->label('Sent At')->placeholder('—'),
                        TextEntry::make('viewed_at')->dateTime()->label('Viewed At')->placeholder('—'),
                        TextEntry::make('downloaded_at')->dateTime()->label('Downloaded At')->placeholder('—'),
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
