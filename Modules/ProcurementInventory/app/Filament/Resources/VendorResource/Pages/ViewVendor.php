<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ProcurementInventory\Filament\Resources\VendorResource;

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
