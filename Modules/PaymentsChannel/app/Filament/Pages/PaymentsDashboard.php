<?php

namespace Modules\PaymentsChannel\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayTransaction;

class PaymentsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'paymentschannel::filament.pages.payments-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $intentBase = PayIntent::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $txBase     = PayTransaction::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $totalVolumeToday = (clone $intentBase)
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('amount');

        $totalVolumeMtd = (clone $intentBase)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->sum('amount');

        $successToday = (clone $intentBase)
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $failedToday = (clone $intentBase)
            ->whereDate('created_at', $today)
            ->where('status', 'failed')
            ->count();

        $mtdTotal = (clone $intentBase)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $mtdSuccess = (clone $intentBase)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->count();

        $successRateMtd = $mtdTotal > 0
            ? round(($mtdSuccess / $mtdTotal) * 100, 1)
            : 0;

        $byProvider = (clone $intentBase)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->selectRaw('provider, SUM(amount) as total')
            ->groupBy('provider')
            ->pluck('total', 'provider')
            ->toArray();

        $recentTransactions = (clone $txBase)
            ->latest('processed_at')
            ->limit(10)
            ->get(['provider', 'transaction_type', 'amount', 'status', 'processed_at']);

        return compact(
            'totalVolumeToday',
            'totalVolumeMtd',
            'successToday',
            'failedToday',
            'successRateMtd',
            'byProvider',
            'recentTransactions'
        );
    }

    public function getTitle(): string
    {
        return 'Payments Dashboard';
    }
}
