<?php

namespace Modules\CommunicationCentre\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\CommunicationCentre\Models\CommMessage;

class AnalyticsService
{
    /**
     * Get delivery statistics for a given period.
     */
    public function getDeliveryStats(?int $companyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $query = CommMessage::whereBetween('created_at', [$startDate, $endDate]);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $totalMessages = $query->count();

        $statusStats = $query->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status');

        $deliveryRate = $totalMessages > 0
            ? round(($statusStats['delivered'] ?? 0) / $totalMessages * 100, 2)
            : 0;

        return [
            'total_messages' => $totalMessages,
            'status_counts' => $statusStats->toArray(),
            'delivery_rate' => $deliveryRate,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get channel performance statistics.
     */
    public function getChannelStats(?int $companyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $stats = CommMessage::whereBetween('created_at', [$startDate, $endDate])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy('channel')
            ->selectRaw('channel, 
                count(*) as total,
                sum(case when status = "delivered" then 1 else 0 end) as delivered,
                sum(case when status = "failed" then 1 else 0 end) as failed,
                sum(case when status = "sent" then 1 else 0 end) as sent,
                round((sum(case when status = "delivered" then 1 else 0 end) / count(*) * 100), 2) as delivery_rate')
            ->get()
            ->keyBy('channel');

        return [
            'channels' => $stats->toArray(),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get provider performance statistics.
     */
    public function getProviderStats(?int $companyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $stats = CommMessage::whereBetween('created_at', [$startDate, $endDate])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy('provider')
            ->selectRaw('provider, 
                count(*) as total,
                sum(case when status = "delivered" then 1 else 0 end) as delivered,
                sum(case when status = "failed" then 1 else 0 end) as failed,
                round((sum(case when status = "delivered" then 1 else 0 end) / count(*) * 100), 2) as delivery_rate')
            ->get()
            ->keyBy('provider');

        return [
            'providers' => $stats->toArray(),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get template performance statistics.
     */
    public function getTemplateStats(?int $companyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $stats = CommMessage::whereBetween('created_at', [$startDate, $endDate])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereNotNull('template_id')
            ->groupBy('template_id')
            ->selectRaw('template_id, 
                count(*) as total,
                sum(case when status = "delivered" then 1 else 0 end) as delivered,
                sum(case when status = "failed" then 1 else 0 end) as failed,
                round((sum(case when status = "delivered" then 1 else 0 end) / count(*) * 100), 2) as delivery_rate')
            ->with('template:id,code,name')
            ->get();

        return [
            'templates' => $stats->toArray(),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get daily message volume.
     */
    public function getDailyVolume(?int $companyId = null, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        $dailyStats = CommMessage::whereBetween('created_at', [$startDate, $endDate])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, 
                count(*) as total,
                sum(case when status = "delivered" then 1 else 0 end) as delivered,
                sum(case when status = "failed" then 1 else 0 end) as failed')
            ->orderBy('date')
            ->get();

        return [
            'daily_volume' => $dailyStats->toArray(),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $days,
            ],
        ];
    }

    /**
     * Get delivery performance over time.
     */
    public function getPerformanceTrend(?int $companyId = null, string $period = 'week'): array
    {
        $groupBy = match ($period) {
            'day' => 'DATE(created_at)',
            'week' => 'YEARWEEK(created_at)',
            'month' => 'DATE_FORMAT(created_at, "%Y-%m")',
            default => 'YEARWEEK(created_at)'
        };

        $trendData = CommMessage::when($companyId, function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
            ->groupBy(DB::raw($groupBy))
            ->selectRaw('COUNT(*) as total_messages,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                ROUND((SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) / COUNT(*) * 100), 2) as delivery_rate')
            ->orderBy(DB::raw($groupBy))
            ->get();

        return [
            'trend' => $trendData->toArray(),
            'period' => $period,
        ];
    }

    /**
     * Get failure analysis.
     */
    public function getFailureAnalysis(?int $companyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $failures = CommMessage::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'failed')
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy('error_message')
            ->selectRaw('error_message, count(*) as count')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'failure_analysis' => $failures->toArray(),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get overall dashboard statistics.
     */
    public function getDashboardStats(?int $companyId = null): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        return [
            'today' => $this->getDeliveryStats($companyId, $today, now()),
            'this_week' => $this->getDeliveryStats($companyId, $thisWeek, now()),
            'this_month' => $this->getDeliveryStats($companyId, $thisMonth, now()),
            'all_time' => $this->getDeliveryStats($companyId),
            'top_channels' => $this->getChannelStats($companyId, now()->subDays(7), now()),
            'recent_failures' => $this->getFailureAnalysis($companyId, now()->subDays(7), now()),
        ];
    }
}
