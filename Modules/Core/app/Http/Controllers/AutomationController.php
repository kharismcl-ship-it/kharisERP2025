<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Models\AutomationLog;
use Modules\Core\Models\AutomationSetting;
use Modules\Core\Services\AutomationService;

class AutomationController extends Controller
{
    protected AutomationService $automationService;

    public function __construct(AutomationService $automationService)
    {
        $this->automationService = $automationService;
    }

    /**
     * Display a listing of automation settings.
     */
    public function index(Request $request): View
    {
        $automations = AutomationSetting::with(['company', 'logs'])
            ->orderBy('module')
            ->orderBy('action')
            ->get();

        return view('core::automations.index', compact('automations'));
    }

    /**
     * Display the specified automation setting.
     */
    public function show(AutomationSetting $automation_setting): View
    {
        $automation_setting->load(['company', 'logs' => function ($query) {
            $query->orderBy('started_at', 'desc')->limit(20);
        }]);

        return view('core::automations.show', compact('automation_setting'));
    }

    /**
     * Display automation logs.
     */
    public function logs(Request $request): View
    {
        $logs = AutomationLog::with(['automationSetting.company'])
            ->orderBy('started_at', 'desc')
            ->paginate(50);

        return view('core::automations.logs', compact('logs'));
    }

    /**
     * Manually run an automation.
     */
    public function run(AutomationSetting $automation_setting, Request $request): JsonResponse
    {
        if (! $automation_setting->is_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Automation is disabled',
            ], 400);
        }

        try {
            $result = $this->automationService->executeAutomation(
                $automation_setting->module,
                $automation_setting->action,
                $automation_setting->company_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Automation executed successfully',
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute automation: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle automation status.
     */
    public function toggle(AutomationSetting $automation_setting, Request $request): JsonResponse
    {
        $automation_setting->update([
            'is_enabled' => ! $automation_setting->is_enabled,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Automation '.($automation_setting->is_enabled ? 'enabled' : 'disabled'),
            'is_enabled' => $automation_setting->is_enabled,
        ]);
    }
}
