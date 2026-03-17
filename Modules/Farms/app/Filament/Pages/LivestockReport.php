<?php

namespace Modules\Farms\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\LivestockHealthRecord;

class LivestockReport extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 92;

    protected static ?string $navigationLabel = 'Livestock Report';

    protected string $view = 'farms::filament.pages.livestock-report';

    public array $healthRows = [];
    public array $summary    = [];

    public function mount(): void
    {
        $this->loadReport();
    }

    public function table(Table $table): Table
    {
        $companyId = auth()->user()?->current_company_id;

        return $table
            ->query(
                LivestockBatch::query()
                    ->with('farm')
                    ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                    ->orderBy('status')
            )
            ->heading('Livestock Batches')
            ->columns([
                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('animal_type')
                    ->label('Animal Type')
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->sortable(),
                TextColumn::make('batch_reference')
                    ->label('Batch Ref')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'closed' => 'gray',
                        default  => 'warning',
                    }),
                TextColumn::make('initial_count')
                    ->label('Initial')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_count')
                    ->label('Current')
                    ->numeric()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                TextColumn::make('mortality')
                    ->label('Mortality')
                    ->state(fn ($record) => $record->initial_count - $record->current_count)
                    ->numeric()
                    ->color(fn ($state) => $state > 0 ? 'danger' : null),
                TextColumn::make('mortality_rate')
                    ->label('Rate %')
                    ->state(fn ($record) => $record->initial_count > 0
                        ? round((($record->initial_count - $record->current_count) / $record->initial_count) * 100, 1)
                        : 0
                    )
                    ->suffix('%')
                    ->color(fn ($state) => $state > 5 ? 'danger' : null),
                TextColumn::make('acquisition_date')
                    ->label('Entry Date')
                    ->date()
                    ->sortable(),
            ])
            ->striped();
    }

    public function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;
        $scope     = fn ($q) => $q->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $this->summary = [
            'total_batches'  => LivestockBatch::query()->tap($scope)->count(),
            'active_batches' => LivestockBatch::query()->tap($scope)->where('status', 'active')->count(),
            'total_animals'  => LivestockBatch::query()->tap($scope)->where('status', 'active')->sum('current_count'),
            'health_alerts'  => LivestockHealthRecord::query()->tap($scope)
                ->whereNotNull('next_due_date')
                ->whereDate('next_due_date', '<=', now()->addDays(14))
                ->count(),
        ];

        $this->healthRows = LivestockHealthRecord::query()
            ->with(['livestockBatch', 'livestockBatch.farm'])
            ->tap($scope)
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', now()->addDays(14))
            ->orderBy('next_due_date')
            ->get()
            ->map(fn ($h) => [
                'farm'      => $h->livestockBatch?->farm?->name ?? '—',
                'batch'     => $h->livestockBatch?->batch_reference ?? '—',
                'treatment' => $h->treatment_type,
                'next_due'  => $h->next_due_date?->format('Y-m-d'),
                'overdue'   => $h->next_due_date?->isPast(),
            ])->toArray();
    }
}
