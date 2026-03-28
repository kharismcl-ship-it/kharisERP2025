<x-filament-panels::page>
    {{-- Stats widget spans full width --}}
    @livewire(\Modules\Requisition\Filament\Widgets\RequisitionStatsWidget::class)

    {{-- Spend and cycle time widgets side-by-side --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @livewire(\Modules\Requisition\Filament\Widgets\RequisitionSpendWidget::class)
        @livewire(\Modules\Requisition\Filament\Widgets\RequisitionCycleTimeWidget::class)
    </div>

    {{-- Trend chart full width --}}
    <div class="mt-6">
        @livewire(\Modules\Requisition\Filament\Widgets\RequisitionChartWidget::class)
    </div>
</x-filament-panels::page>