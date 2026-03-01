<?php

namespace Modules\Finance\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\InvoiceResource;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

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
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('customer_id')->label('Customer Record ID')->placeholder('—'),
                    ]),

                Section::make('Invoice Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice_number')->weight('bold'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'     => 'gray',
                                'sent'      => 'info',
                                'paid'      => 'success',
                                'overdue'   => 'danger',
                                'cancelled' => 'gray',
                                default     => 'gray',
                            }),
                        TextEntry::make('invoice_date')->date()->label('Invoice Date'),
                        TextEntry::make('due_date')->date()->label('Due Date')->placeholder('—'),
                    ]),

                Section::make('Amounts')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sub_total')->money('GHS')->label('Subtotal'),
                        TextEntry::make('tax_total')->money('GHS')->label('Tax'),
                        TextEntry::make('total')->money('GHS')->label('Total')->weight('bold'),
                    ]),

                Section::make('Module Reference')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('hostel_id')->label('Hostel ID')->placeholder('—'),
                        TextEntry::make('farm_id')->label('Farm ID')->placeholder('—'),
                        TextEntry::make('construction_project_id')->label('Construction Project ID')->placeholder('—'),
                        TextEntry::make('plant_id')->label('Plant / Manufacturing ID')->placeholder('—'),
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
