<?php

namespace Modules\CommunicationCentre\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\CommunicationCentre\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get overall delivery statistics.
     */
    public function deliveryStats(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getDeliveryStats($companyId, $startDate, $endDate);

        return response()->json($stats);
    }

    /**
     * Get channel performance statistics.
     */
    public function channelStats(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getChannelStats($companyId, $startDate, $endDate);

        return response()->json($stats);
    }

    /**
     * Get provider performance statistics.
     */
    public function providerStats(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getProviderStats($companyId, $startDate, $endDate);

        return response()->json($stats);
    }

    /**
     * Get template performance statistics.
     */
    public function templateStats(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getTemplateStats($companyId, $startDate, $endDate);

        return response()->json($stats);
    }

    /**
     * Get daily message volume.
     */
    public function dailyVolume(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $days = $request->days ?? 30;
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getDailyVolume($companyId, $days);

        return response()->json($stats);
    }

    /**
     * Get performance trends.
     */
    public function performanceTrend(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:day,week,month',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $period = $request->period ?? 'week';
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getPerformanceTrend($companyId, $period);

        return response()->json($stats);
    }

    /**
     * Get failure analysis.
     */
    public function failureAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $companyId = $request->company_id;

        $stats = $this->analyticsService->getFailureAnalysis($companyId, $startDate, $endDate);

        return response()->json($stats);
    }

    /**
     * Get comprehensive dashboard statistics.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'nullable|integer|exists:companies,id',
        ]);

        $companyId = $request->company_id;

        $stats = $this->analyticsService->getDashboardStats($companyId);

        return response()->json($stats);
    }
}
