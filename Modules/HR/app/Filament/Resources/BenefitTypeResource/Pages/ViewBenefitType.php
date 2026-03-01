<?php

namespace Modules\HR\Filament\Resources\BenefitTypeResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\BenefitTypeResource;
use Modules\HR\Models\BenefitType;

class ViewBenefitType extends ViewRecord
{
    protected static string $resource = BenefitTypeResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Benefit Type Details')
                    ->collapsible()
                    ->columns(['default' => 2, 'xl' => 3])
                    ->schema([
                        TextEntry::make('name')->weight('bold'),
                        TextEntry::make('company.name'),
                        TextEntry::make('category')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'health' => 'success', 'insurance' => 'info',
                                'transport' => 'warning', 'housing' => 'primary', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => BenefitType::CATEGORIES[$state] ?? $state),
                        TextEntry::make('provider')->placeholder('—'),
                        TextEntry::make('employer_contribution')->money('GHS')->placeholder('—'),
                        TextEntry::make('employee_contribution')->money('GHS')->placeholder('Not required'),
                        IconEntry::make('employee_contribution_required')->label('Employee Pays?')->boolean(),
                        IconEntry::make('is_taxable')->label('Taxable')->boolean(),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                    ]),

                Section::make('Description')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}