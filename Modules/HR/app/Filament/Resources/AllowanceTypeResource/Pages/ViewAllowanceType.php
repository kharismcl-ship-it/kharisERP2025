<?php

namespace Modules\HR\Filament\Resources\AllowanceTypeResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\AllowanceTypeResource;

class ViewAllowanceType extends ViewRecord
{
    protected static string $resource = AllowanceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Allowance Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('company.name')->label('Company'),
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('code')->badge()->color('gray'),
                    TextEntry::make('calculation_type')
                        ->label('Calculation Type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'fixed'      => 'primary',
                            'percentage' => 'warning',
                            default      => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                    TextEntry::make('default_amount')
                        ->label('Default Amount (GHS)')
                        ->money('GHS')
                        ->placeholder('—'),
                    TextEntry::make('percentage_value')
                        ->label('Percentage Value')
                        ->suffix('%')
                        ->placeholder('—'),
                    TextEntry::make('gl_account_code')
                        ->label('GL Account Code')
                        ->placeholder('—'),
                ]),

            Section::make('Flags')
                ->columns(3)
                ->schema([
                    IconEntry::make('is_taxable')->label('Taxable')->boolean(),
                    IconEntry::make('is_pensionable')->label('Pensionable')->boolean(),
                    IconEntry::make('is_active')->label('Active')->boolean(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('description')->placeholder('None'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}