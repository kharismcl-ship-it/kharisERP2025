<?php

namespace Modules\Farms\Filament\Resources\FarmResource\Pages;

use EduardoRibeiroDev\FilamentLeaflet\Infolists\MapEntry;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\FarmResource;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;

class ViewFarm extends ViewRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Farm Summary')
                ->description('Year-to-date performance indicators')
                ->columns(5)
                ->schema([
                    TextEntry::make('active_crops')
                        ->label('Active Crops')
                        ->getStateUsing(fn ($record) =>
                            CropCycle::where('farm_id', $record->id)
                                ->whereIn('status', ['preparing', 'growing'])
                                ->count() . ' cycles'
                        )
                        ->weight('bold')
                        ->color('info'),

                    TextEntry::make('total_livestock')
                        ->label('Total Livestock')
                        ->getStateUsing(fn ($record) =>
                            number_format(
                                LivestockBatch::where('farm_id', $record->id)
                                    ->where('status', 'active')
                                    ->sum('current_count')
                            ) . ' animals'
                        )
                        ->weight('bold')
                        ->color('primary'),

                    TextEntry::make('harvest_revenue_ytd')
                        ->label('Harvest Revenue (YTD)')
                        ->getStateUsing(fn ($record) =>
                            'GHS ' . number_format(
                                HarvestRecord::where('farm_id', $record->id)
                                    ->whereYear('harvest_date', now()->year)
                                    ->sum('total_revenue'),
                                2
                            )
                        )
                        ->weight('bold')
                        ->color('success'),

                    TextEntry::make('total_expenses_ytd')
                        ->label('Total Expenses (YTD)')
                        ->getStateUsing(fn ($record) =>
                            'GHS ' . number_format(
                                FarmExpense::where('farm_id', $record->id)
                                    ->whereYear('expense_date', now()->year)
                                    ->sum('amount'),
                                2
                            )
                        )
                        ->weight('bold')
                        ->color('warning'),

                    TextEntry::make('net_profit_ytd')
                        ->label('Net Profit (YTD)')
                        ->getStateUsing(function ($record) {
                            $revenue  = HarvestRecord::where('farm_id', $record->id)->whereYear('harvest_date', now()->year)->sum('total_revenue');
                            $expenses = FarmExpense::where('farm_id', $record->id)->whereYear('expense_date', now()->year)->sum('amount');
                            return 'GHS ' . number_format($revenue - $expenses, 2);
                        })
                        ->weight('bold')
                        ->color(function ($record) {
                            $revenue  = HarvestRecord::where('farm_id', $record->id)->whereYear('harvest_date', now()->year)->sum('total_revenue');
                            $expenses = FarmExpense::where('farm_id', $record->id)->whereYear('expense_date', now()->year)->sum('amount');
                            return ($revenue - $expenses) >= 0 ? 'success' : 'danger';
                        }),
                ]),

            Section::make('Farm Identity')
                ->description('Registration and classification details')
                ->columns(3)
                ->schema([
                    TextEntry::make('name')
                        ->label('Farm Name')
                        ->weight('bold'),

                    TextEntry::make('type')
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'   => 'success',
                            'inactive' => 'gray',
                            'fallow'   => 'warning',
                            default    => 'gray',
                        }),

                    TextEntry::make('location')
                        ->label('Location')
                        ->placeholder('—'),

                    TextEntry::make('total_area')
                        ->label('Total Area')
                        ->formatStateUsing(fn ($state, $record) =>
                            $state ? number_format($state, 2) . ' ' . $record->area_unit : '—'
                        ),

                    TextEntry::make('slug')
                        ->badge()
                        ->color('gray'),
                ]),

            Section::make('Owner Details')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextEntry::make('owner_name')->label('Owner Name')->placeholder('—'),
                    TextEntry::make('owner_phone')->label('Owner Phone')->placeholder('—'),
                ]),

            Section::make('Description & Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
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

            Section::make('Location on Map')
                ->collapsible()
                ->collapsed()
                ->schema([
                    MapEntry::make('map_coordinates')
                        ->latitudeFieldName('latitude')
                        ->longitudeFieldName('longitude')
                        ->height(300)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
