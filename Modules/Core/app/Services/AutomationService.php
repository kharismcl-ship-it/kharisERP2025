<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\AutomationLog;
use Modules\Core\Models\AutomationSetting;

class AutomationService
{
    public function executeAutomation(string $module, string $action, ?int $companyId = null): bool
    {
        $setting = $this->getAutomationSetting($module, $action, $companyId);

        if (! $setting || ! $setting->is_enabled) {
            return false;
        }

        $log = AutomationLog::create([
            'automation_setting_id' => $setting->id,
            'status' => 'pending',
        ]);

        try {
            $log->markAsStarted();

            $result = $this->dispatchAutomation($module, $action, $setting);

            if ($result['success']) {
                $log->markAsCompleted(
                    $result['records_processed'] ?? 0,
                    $result['details'] ?? []
                );
                $setting->markAsRun();
            } else {
                $log->markAsFailed(
                    $result['error'] ?? 'Automation failed',
                    $result['details'] ?? []
                );
            }

            return $result['success'];

        } catch (\Exception $e) {
            Log::error("Automation execution failed: {$module}.{$action}", ['error' => $e->getMessage()]);
            $log->markAsFailed($e->getMessage());

            return false;
        }
    }

    protected function getAutomationSetting(string $module, string $action, ?int $companyId = null): ?AutomationSetting
    {
        $query = AutomationSetting::where('module', $module)
            ->where('action', $action);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->first();
    }

    protected function dispatchAutomation(string $module, string $action, AutomationSetting $setting): array
    {
        // First try module-specific handler
        $handlerClass = "Modules\\{$module}\\Services\\Automation\\{$this->getHandlerClassName($action)}Handler";

        if (class_exists($handlerClass)) {
            return app($handlerClass)->execute($setting);
        }

        // Fallback to Artisan command
        $command = "{$module}:automate-{$action}";
        $parameters = [
            '--company' => $setting->company_id,
        ];

        Artisan::call($command, $parameters);

        return [
            'success' => true,
            'records_processed' => 0, // Commands should handle their own logging
        ];
    }

    protected function getHandlerClassName(string $action): string
    {
        return str_replace('_', '', ucwords($action, '_'));
    }

    public function getAutomationsForModule(string $module): array
    {
        return [
            'HR' => [
                'leave_accrual' => [
                    'name' => 'Leave Balance Accrual',
                    'description' => 'Automatically accrue leave balances for employees',
                    'schedule_types' => ['monthly', 'quarterly', 'yearly'],
                ],
                'attendance_reconciliation' => [
                    'name' => 'Attendance Reconciliation',
                    'description' => 'Reconcile daily attendance records',
                    'schedule_types' => ['daily'],
                ],
            ],
            'Finance' => [
                'invoice_generation' => [
                    'name' => 'Recurring Invoice Generation',
                    'description' => 'Generate recurring invoices automatically',
                    'schedule_types' => ['daily', 'weekly', 'monthly'],
                ],
            ],
            'Hostels' => [
                'billing_cycle_generation' => [
                    'name' => 'Recurring Billing Cycle Generation',
                    'description' => 'Auto-generate billing cycles for active hostel occupants',
                    'schedule_types' => ['daily', 'monthly'],
                ],
                'deposit_reminder' => [
                    'name' => 'Deposit Collection Reminder',
                    'description' => 'Send SMS reminders for pending hostel deposits',
                    'schedule_types' => ['daily', 'weekly'],
                ],
                'overdue_charge_reminder' => [
                    'name' => 'Overdue Charge Reminder',
                    'description' => 'Send SMS reminders for overdue hostel charges',
                    'schedule_types' => ['daily', 'weekly'],
                ],
            ],
        ][$module] ?? [];
    }

    public function initializeModuleAutomations(string $module, int $companyId): void
    {
        $automations = $this->getAutomationsForModule($module);

        foreach ($automations as $action => $config) {
            AutomationSetting::firstOrCreate(
                [
                    'module' => $module,
                    'action' => $action,
                    'company_id' => $companyId,
                ],
                [
                    'is_enabled' => false,
                    'schedule_type' => $config['schedule_types'][0] ?? null,
                    'config' => [],
                ]
            );
        }
    }
}
