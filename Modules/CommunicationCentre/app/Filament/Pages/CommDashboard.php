<?php

namespace Modules\CommunicationCentre\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\CommunicationCentre\Models\CommMessage;

class CommDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'communicationcentre::filament.pages.comm-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $base = CommMessage::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $totalToday = (clone $base)
            ->whereDate('sent_at', $today)
            ->count();

        $totalMtd = (clone $base)
            ->whereBetween('sent_at', [$monthStart, $monthEnd])
            ->count();

        $failedToday = (clone $base)
            ->whereDate('sent_at', $today)
            ->where('status', 'failed')
            ->count();

        $mtdMessages = (clone $base)
            ->whereBetween('sent_at', [$monthStart, $monthEnd])
            ->whereIn('status', ['sent', 'delivered', 'failed'])
            ->count();

        $mtdDelivered = (clone $base)
            ->whereBetween('sent_at', [$monthStart, $monthEnd])
            ->where('status', 'delivered')
            ->count();

        $deliveryRateMtd = $mtdMessages > 0
            ? round(($mtdDelivered / $mtdMessages) * 100, 1)
            : 0;

        $byChannel = (clone $base)
            ->whereBetween('sent_at', [$monthStart, $monthEnd])
            ->selectRaw('channel, count(*) as total')
            ->groupBy('channel')
            ->pluck('total', 'channel')
            ->toArray();

        $recentFailed = (clone $base)
            ->where('status', 'failed')
            ->latest('sent_at')
            ->limit(10)
            ->get(['to_name', 'channel', 'subject', 'error_message', 'sent_at']);

        return compact(
            'totalToday',
            'totalMtd',
            'failedToday',
            'deliveryRateMtd',
            'byChannel',
            'recentFailed'
        );
    }

    public function getTitle(): string
    {
        return 'Communication Dashboard';
    }
}
