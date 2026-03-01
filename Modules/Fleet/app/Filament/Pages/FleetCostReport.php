<?php

namespace Modules\Fleet\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Fleet\Services\FleetService;

class FleetCostReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Cost Report';

    protected string $view = 'fleet::filament.pages.fleet-cost-report';

    public string $period = 'ytd';
    public string $from   = '';
    public string $to     = '';
    public array  $rows   = [];

    public function mount(): void
    {
        $this->from = now()->startOfYear()->toDateString();
        $this->to   = now()->toDateString();
        $this->loadReport();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;

        match ($period) {
            'mtd'    => [$this->from, $this->to] = [now()->startOfMonth()->toDateString(), now()->toDateString()],
            'qtd'    => [$this->from, $this->to] = [now()->startOfQuarter()->toDateString(), now()->toDateString()],
            'ytd'    => [$this->from, $this->to] = [now()->startOfYear()->toDateString(), now()->toDateString()],
            'last30' => [$this->from, $this->to] = [now()->subDays(30)->toDateString(), now()->toDateString()],
            default  => null,
        };

        $this->loadReport();
    }

    protected function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;

        if (! $companyId) {
            $this->rows = [];
            return;
        }

        $this->rows = app(FleetService::class)
            ->costSummary($companyId, $this->from, $this->to)
            ->toArray();
    }
}