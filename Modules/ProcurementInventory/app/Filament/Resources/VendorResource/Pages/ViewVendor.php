<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ProcurementInventory\Filament\Resources\VendorResource;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vendor Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'active'   => 'success',
                            'inactive' => 'warning',
                            'blocked'  => 'danger',
                            default    => 'gray',
                        }),
                    TextEntry::make('email')->placeholder('—'),
                    TextEntry::make('phone')->placeholder('—'),
                    TextEntry::make('tax_number')->label('Tax / VAT Number')->placeholder('—'),
                    TextEntry::make('currency'),
                    TextEntry::make('company.name')->label('Company'),
                ]),

            Section::make('Address')
                ->columns(2)
                ->schema([
                    TextEntry::make('address')->placeholder('—'),
                    TextEntry::make('city')->placeholder('—'),
                    TextEntry::make('country')->placeholder('—'),
                ]),

            Section::make('Contact Person')
                ->columns(2)
                ->schema([
                    TextEntry::make('contact_person')->placeholder('—'),
                    TextEntry::make('contact_phone')->placeholder('—'),
                    TextEntry::make('contact_email')->placeholder('—'),
                ]),

            Section::make('Payment & Banking')
                ->columns(2)
                ->schema([
                    TextEntry::make('payment_terms')->suffix(' days'),
                    TextEntry::make('bank_name')->placeholder('—'),
                    TextEntry::make('bank_account_number')->placeholder('—'),
                    TextEntry::make('bank_branch')->placeholder('—'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->placeholder('None'),
                ]),

            Section::make('Procurement Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('total_pos')
                        ->label('Total POs')
                        ->getStateUsing(fn ($record) => $record->purchaseOrders()->count()),

                    TextEntry::make('open_pos')
                        ->label('Open POs')
                        ->getStateUsing(fn ($record) => $record->purchaseOrders()
                            ->whereIn('status', ['submitted', 'approved', 'ordered', 'partially_received'])
                            ->count()),

                    TextEntry::make('total_spend')
                        ->label('Total Spend (GHS)')
                        ->getStateUsing(fn ($record) => number_format(
                            $record->purchaseOrders()
                                ->whereIn('status', ['received', 'closed'])
                                ->sum('total'),
                            2
                        )),

                    TextEntry::make('mtd_spend')
                        ->label('Spend This Month (GHS)')
                        ->getStateUsing(fn ($record) => number_format(
                            $record->purchaseOrders()
                                ->whereIn('status', ['received', 'closed'])
                                ->whereMonth('po_date', now()->month)
                                ->whereYear('po_date', now()->year)
                                ->sum('total'),
                            2
                        )),

                    TextEntry::make('ytd_spend')
                        ->label('Spend This Year (GHS)')
                        ->getStateUsing(fn ($record) => number_format(
                            $record->purchaseOrders()
                                ->whereIn('status', ['received', 'closed'])
                                ->whereYear('po_date', now()->year)
                                ->sum('total'),
                            2
                        )),

                    TextEntry::make('last_po_date')
                        ->label('Last PO Date')
                        ->getStateUsing(fn ($record) => optional(
                            $record->purchaseOrders()->latest('po_date')->first()
                        )->po_date?->format('d M Y') ?? '—'),
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
