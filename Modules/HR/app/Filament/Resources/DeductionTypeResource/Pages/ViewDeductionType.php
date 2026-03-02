<?php

namespace Modules\HR\Filament\Resources\DeductionTypeResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\DeductionTypeResource;
use Modules\HR\Models\DeductionType;

class ViewDeductionType extends ViewRecord
{
    protected static string $resource = DeductionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Deduction Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('company.name')->label('Company'),
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('code')->badge()->color('gray'),
                    TextEntry::make('category')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'tax'             => 'danger',
                            'social_security' => 'warning',
                            'pension'         => 'info',
                            'loan'            => 'primary',
                            default           => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => DeductionType::CATEGORIES[$state] ?? ucfirst($state)),
                    TextEntry::make('calculation_type')
                        ->label('Calculation Type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'fixed'      => 'primary',
                            'percentage' => 'warning',
                            default      => 'gray',
                        }),
                    TextEntry::make('default_amount')
                        ->label('Default Amount (GHS)')
                        ->money('GHS')
                        ->placeholder('—'),
                    TextEntry::make('percentage_value')
                        ->label('Percentage Value')
                        ->suffix('%')
                        ->placeholder('—'),
                    TextEntry::make('gl_account_code')
                        ->label('GL / Liability Account Code')
                        ->placeholder('—'),
                ]),

            Section::make('Status')
                ->columns(2)
                ->schema([
                    IconEntry::make('is_active')->label('Active')->boolean(),
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