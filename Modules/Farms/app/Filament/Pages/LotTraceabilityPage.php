<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Livewire\Attributes\Url;
use Modules\Farms\Models\FarmProduceLot;

class LotTraceabilityPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Lot Traceability';

    protected static ?int $navigationSort = 28;

    protected string $view = 'farms::filament.pages.lot-traceability';

    #[Url]
    public string $lotSearch = '';

    public ?FarmProduceLot $lot = null;
    public ?array $chain = null;
    public ?string $searchError = null;

    public function searchLot(): void
    {
        $this->searchError = null;
        $this->lot         = null;
        $this->chain       = null;

        if (empty($this->lotSearch)) {
            return;
        }

        $record = FarmProduceLot::where('company_id', Filament::getTenant()?->id)
            ->where('lot_number', strtoupper(trim($this->lotSearch)))
            ->first();

        if (! $record) {
            $this->searchError = "No lot found for: {$this->lotSearch}";

            return;
        }

        $this->lot   = $record;
        $rawChain    = $record->traceabilityChain();

        // Make it blade-serializable
        $this->chain = [
            'lot'        => $record,
            'harvest'    => $rawChain['harvest'],
            'crop_cycle' => $rawChain['crop_cycle'],
            'farm'       => $rawChain['farm'],
            'inputs'     => $rawChain['inputs'],
            'orders'     => $rawChain['orders'],
        ];
    }
}