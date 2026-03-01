<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\LivestockBatchResource;

class ViewLivestockBatch extends ViewRecord
{
    protected static string $resource = LivestockBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Livestock Summary')
                ->columns(4)
                ->schema([
                    TextEntry::make('current_count')
                        ->label('Current Count')
                        ->getStateUsing(fn ($record) => number_format($record->current_count) . ' animals')
                        ->weight('bold')
                        ->color('success'),

                    TextEntry::make('mortality_rate_kpi')
                        ->label('Mortality Rate')
                        ->getStateUsing(fn ($record) => $record->getMortalityRateAttribute() . '%')
                        ->weight('bold')
                        ->color(fn ($record) => $record->getMortalityRateAttribute() > 10 ? 'danger' : 'success'),

                    TextEntry::make('acquisition_cost_kpi')
                        ->label('Acquisition Cost')
                        ->getStateUsing(fn ($record) => 'GHS ' . number_format($record->acquisition_cost, 2))
                        ->weight('bold')
                        ->color('warning'),

                    TextEntry::make('cost_per_head')
                        ->label('Cost Per Head')
                        ->getStateUsing(fn ($record) =>
                            $record->current_count > 0
                                ? 'GHS ' . number_format($record->acquisition_cost / $record->current_count, 2)
                                : '—'
                        )
                        ->weight('bold'),
                ]),

            Section::make('Batch Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('batch_reference')
                        ->label('Reference')
                        ->badge()
                        ->color('gray')
                        ->copyable(),

                    TextEntry::make('animal_type')
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('breed')
                        ->placeholder('—'),

                    TextEntry::make('farm.name')
                        ->label('Farm'),

                    TextEntry::make('acquisition_date')
                        ->date('d M Y'),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'      => 'success',
                            'sold'        => 'info',
                            'slaughtered' => 'warning',
                            'deceased'    => 'danger',
                            default       => 'gray',
                        }),
                ]),

            Section::make('Count History')
                ->columns(2)
                ->schema([
                    TextEntry::make('initial_count')
                        ->label('Initial Count')
                        ->numeric(),

                    TextEntry::make('current_count')
                        ->label('Current Count')
                        ->numeric(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('No notes'),
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