<?php

namespace Modules\ProcurementInventory\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\Vendor;

class LocalContentReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 80;

    protected static ?string $navigationLabel = 'Local Content Report';

    protected string $view = 'procurementinventory::filament.pages.local-content-report';

    public int    $year   = 0;
    public string $period = 'annual';

    public float      $totalSpend        = 0.0;
    public float      $localSpend        = 0.0;
    public float      $localPct          = 0.0;
    public float      $localContentTarget = 40.0;
    public Collection $diversityBreakdown;

    public function mount(): void
    {
        $this->year              = (int) date('Y');
        $this->diversityBreakdown = collect();
        $this->loadData();
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            TextInput::make('year')
                ->label('Year')
                ->numeric()
                ->default(date('Y'))
                ->live(),

            Select::make('period')
                ->label('Period')
                ->options([
                    'quarterly' => 'Quarterly',
                    'annual'    => 'Annual',
                ])
                ->default('annual')
                ->live(),
        ]);
    }

    public function updatedYear(): void   { $this->loadData(); }
    public function updatedPeriod(): void { $this->loadData(); }

    public function loadData(): void
    {
        $pos = PurchaseOrder::whereIn('status', ['received', 'closed'])
            ->whereYear('po_date', $this->year)
            ->with('vendor')
            ->get();

        $this->totalSpend = (float) $pos->sum('total');

        $localPos         = $pos->filter(fn ($po) => $po->vendor?->is_local === true);
        $this->localSpend = (float) $localPos->sum('total');
        $this->localPct   = $this->totalSpend > 0
            ? round($this->localSpend / $this->totalSpend * 100, 2)
            : 0;

        // Diversity class breakdown
        $classes = [
            'women_owned' => 'Women-Owned',
            'sme'         => 'SME',
            'minority'    => 'Minority-Owned',
            'local'       => 'Local / Indigenous',
            null          => 'Standard',
        ];

        $this->diversityBreakdown = collect($classes)->map(function ($label, $class) use ($pos) {
            $group = $pos->filter(fn ($po) => $po->vendor?->diversity_class === $class);
            $spend = (float) $group->sum('total');
            $pct   = $this->totalSpend > 0 ? round($spend / $this->totalSpend * 100, 2) : 0;
            return [
                'class'    => $class ?? 'standard',
                'label'    => $label,
                'spend'    => $spend,
                'pct'      => $pct,
                'vendors'  => $group->pluck('vendor_id')->unique()->count(),
            ];
        })->values();
    }
}