<?php

namespace Modules\ProcurementInventory\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Models\Warehouse;

class InventoryValuationReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 75;

    protected static ?string $navigationLabel = 'Inventory Valuation';

    protected string $view = 'procurementinventory::filament.pages.inventory-valuation';

    public ?string $dateAsAt       = null;
    public ?int    $warehouseId    = null;

    public Collection $rows;
    public float      $grandTotal   = 0.0;

    public function mount(): void
    {
        $this->dateAsAt = now()->toDateString();
        $this->rows     = collect();
        $this->loadData();
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            DatePicker::make('dateAsAt')
                ->label('As At Date')
                ->default(now()->toDateString())
                ->live(),

            Select::make('warehouseId')
                ->label('Warehouse')
                ->options(fn () => Warehouse::where('is_active', true)->pluck('name', 'id')->toArray())
                ->placeholder('All Warehouses')
                ->nullable()
                ->live(),
        ]);
    }

    public function updatedDateAsAt(): void
    {
        $this->loadData();
    }

    public function updatedWarehouseId(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $query = StockLevel::with(['item.category', 'warehouse']);

        if ($this->warehouseId) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        $levels = $query->get();

        $this->rows = $levels
            ->filter(fn ($sl) => (float) $sl->quantity_on_hand > 0)
            ->map(fn ($sl) => [
                'sku'           => $sl->item?->sku,
                'item_name'     => $sl->item?->name,
                'category'      => $sl->item?->category?->name ?? '—',
                'warehouse'     => $sl->warehouse?->name ?? '—',
                'uom'           => $sl->item?->unit_of_measure ?? '—',
                'qty_on_hand'   => (float) $sl->quantity_on_hand,
                'avg_unit_cost' => (float) $sl->average_unit_cost,
                'total_value'   => (float) $sl->total_value,
            ])
            ->sortBy(['category', 'item_name'])
            ->values();

        $this->grandTotal = $this->rows->sum('total_value');
    }

    public function getGroupedRows(): Collection
    {
        return $this->rows->groupBy('category');
    }
}