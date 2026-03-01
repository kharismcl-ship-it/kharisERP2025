<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\CropCycleResource;
use Modules\Farms\Models\FarmExpense;

class ViewCropCycle extends ViewRecord
{
    protected static string $resource = CropCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Crop Cycle P&L')
                ->description('Revenue, cost, and yield performance')
                ->columns(5)
                ->schema([
                    TextEntry::make('total_harvest_revenue')
                        ->label('Total Revenue')
                        ->getStateUsing(fn ($record) =>
                            'GHS ' . number_format($record->getTotalRevenueAttribute(), 2)
                        )
                        ->weight('bold')
                        ->color('success'),

                    TextEntry::make('total_expenses')
                        ->label('Total Expenses')
                        ->getStateUsing(fn ($record) =>
                            'GHS ' . number_format(
                                FarmExpense::where('crop_cycle_id', $record->id)->sum('amount'),
                                2
                            )
                        )
                        ->weight('bold')
                        ->color('warning'),

                    TextEntry::make('net_profit')
                        ->label('Net Profit')
                        ->getStateUsing(function ($record) {
                            $revenue = $record->getTotalRevenueAttribute();
                            $cost    = FarmExpense::where('crop_cycle_id', $record->id)->sum('amount');
                            return 'GHS ' . number_format($revenue - $cost, 2);
                        })
                        ->weight('bold')
                        ->color(function ($record) {
                            $revenue = $record->getTotalRevenueAttribute();
                            $cost    = FarmExpense::where('crop_cycle_id', $record->id)->sum('amount');
                            return ($revenue - $cost) >= 0 ? 'success' : 'danger';
                        }),

                    TextEntry::make('yield_achievement')
                        ->label('Yield Achievement')
                        ->getStateUsing(fn ($record) =>
                            $record->expected_yield && $record->expected_yield > 0
                                ? number_format(($record->getTotalHarvestedAttribute() / $record->expected_yield) * 100, 1) . '%'
                                : '—'
                        )
                        ->weight('bold'),

                    TextEntry::make('days_to_harvest')
                        ->label('Days to Harvest')
                        ->getStateUsing(fn ($record) =>
                            $record->expected_harvest_date && $record->status === 'growing'
                                ? now()->diffInDays($record->expected_harvest_date, false) . ' days'
                                : '—'
                        )
                        ->color(function ($record) {
                            if (! $record->expected_harvest_date || $record->status !== 'growing') return null;
                            $days = now()->diffInDays($record->expected_harvest_date, false);
                            return $days < 0 ? 'danger' : ($days <= 14 ? 'warning' : 'success');
                        }),
                ]),

            Section::make('Crop Identity')
                ->columns(3)
                ->schema([
                    TextEntry::make('crop_name')->label('Crop')->weight('bold'),
                    TextEntry::make('variety')->placeholder('—'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'growing'   => 'info',
                            'harvested' => 'success',
                            'preparing' => 'gray',
                            'failed'    => 'danger',
                            default     => 'gray',
                        }),
                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('plot.name')->label('Plot')->placeholder('—'),
                    TextEntry::make('season')->placeholder('—'),
                ]),

            Section::make('Dates')
                ->columns(3)
                ->schema([
                    TextEntry::make('planting_date')->date('d M Y'),
                    TextEntry::make('expected_harvest_date')->date('d M Y')->label('Expected Harvest')->placeholder('—'),
                    TextEntry::make('actual_harvest_date')->date('d M Y')->label('Actual Harvest')->placeholder('—'),
                ]),

            Section::make('Area & Yield Target')
                ->columns(3)
                ->schema([
                    TextEntry::make('planted_area')
                        ->label('Planted Area')
                        ->formatStateUsing(fn ($state, $record) =>
                            $state ? number_format($state, 2) . ' ' . $record->planted_area_unit : '—'
                        ),

                    TextEntry::make('expected_yield')
                        ->label('Expected Yield')
                        ->formatStateUsing(fn ($state, $record) =>
                            $state ? number_format($state, 2) . ' ' . ($record->yield_unit ?? '') : '—'
                        ),

                    TextEntry::make('actual_harvested')
                        ->label('Total Harvested')
                        ->getStateUsing(fn ($record) =>
                            number_format($record->getTotalHarvestedAttribute(), 2) . ' ' . ($record->yield_unit ?? '')
                        ),
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