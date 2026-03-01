<?php

namespace Modules\Finance\Filament\Resources\TaxRateResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\TaxRateResource;
use Modules\Finance\Models\TaxRate;

class ViewTaxRate extends ViewRecord
{
    protected static string $resource = TaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tax Rate Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')->weight('bold'),
                        TextEntry::make('name'),
                        TextEntry::make('rate')->suffix('%'),
                        TextEntry::make('type')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => TaxRate::TYPES[$state] ?? $state)
                            ->color(fn (string $state) => match ($state) {
                                'vat'         => 'info',
                                'nhil'        => 'warning',
                                'getf'        => 'primary',
                                'withholding' => 'danger',
                                default       => 'gray',
                            }),
                        TextEntry::make('applies_to')
                            ->badge()
                            ->color('gray'),
                        IconEntry::make('is_active')->boolean()->label('Active'),
                        TextEntry::make('company.name')->label('Company')->placeholder('System-wide'),
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
