<?php

namespace Modules\Farms\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Models\HarvestRecord;

class CropYieldReport extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 91;

    protected static ?string $navigationLabel = 'Crop Yield Report';

    protected string $view = 'farms::filament.pages.crop-yield-report';

    public function table(Table $table): Table
    {
        $companyId = auth()->user()?->current_company_id;

        return $table
            ->query(
                HarvestRecord::query()
                    ->with(['farm', 'cropCycle'])
                    ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            )
            ->heading('Harvest Records')
            ->columns([
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cropCycle.crop_name')
                    ->label('Crop Cycle')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('harvest_date')
                    ->label('Harvest Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric(3)
                    ->sortable()
                    ->summarize(
                        Sum::make()->numeric(3)->label('Total Harvested')
                    ),
                TextColumn::make('unit')
                    ->label('Unit'),
                TextColumn::make('unit_price')
                    ->label('Unit Price (GHS)')
                    ->numeric(2)
                    ->prefix('GHS '),
                TextColumn::make('total_revenue')
                    ->label('Revenue (GHS)')
                    ->numeric(2)
                    ->prefix('GHS ')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->color('success')
                    ->summarize(
                        Sum::make()->numeric(2)->prefix('GHS ')->label('Total Revenue')
                    ),
                TextColumn::make('buyer_name')
                    ->label('Buyer')
                    ->placeholder('—'),
            ])
            ->defaultSort('harvest_date', 'desc')
            ->filters([
                SelectFilter::make('year')
                    ->label('Year')
                    ->options(fn () => $this->getYearOptions())
                    ->default((string) now()->year)
                    ->modifyQueryUsing(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'] ?? now()->year,
                            fn ($q, $year) => $q->whereYear('harvest_date', $year)
                        )
                    ),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(1)
            ->striped();
    }

    protected function getYearOptions(): array
    {
        $options = [];
        for ($y = now()->year; $y >= 2022; $y--) {
            $options[(string) $y] = (string) $y;
        }

        return $options;
    }
}
