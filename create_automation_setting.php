<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Core\Models\AutomationSetting;

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Creating Automation Setting ===\n\n";

try {
    // Create the automation setting
    $setting = AutomationSetting::create([
        'module' => 'HR',
        'action' => 'leave_accrual',
        'company_id' => 1,
        'is_enabled' => true,
        'schedule_type' => 'monthly',
        'schedule_day' => 1, // Run on the 1st of each month
        'config' => [
            'accrual_multiplier' => 1.0,
            'skip_inactive_employees' => true,
        ],
        'last_run_at' => null,
        'next_run_at' => now()->addMonth()->startOfMonth(),
    ]);

    echo "Automation Setting Created Successfully!\n";
    echo "- ID: {$setting->id}\n";
    echo "- Module: {$setting->module}\n";
    echo "- Action: {$setting->action}\n";
    echo "- Company ID: {$setting->company_id}\n";
    echo '- Is Enabled: '.($setting->is_enabled ? 'YES' : 'NO')."\n";
    echo "- Schedule Type: {$setting->schedule_type}\n";
    echo "- Schedule Day: {$setting->schedule_day}\n\n";

    // Now test the automation
    echo "Testing automation with the new setting...\n";

    $automationService = app(Modules\Core\Services\AutomationService::class);
    $result = $automationService->executeAutomation('HR', 'leave_accrual', 1);

    echo 'Automation Result: '.gettype($result)."\n";
    if (is_array($result)) {
        echo '- Success: '.($result['success'] ? 'YES' : 'NO')."\n";
        echo '- Records Processed: '.$result['records_processed']."\n";

        if (isset($result['details'])) {
            echo '- Employees Processed: '.$result['details']['employees_processed']."\n";
            if (! empty($result['details']['errors'])) {
                echo '- Errors: '.print_r($result['details']['errors'], true)."\n";
            }
        }
    } else {
        echo '- Result: '.($result ? 'TRUE' : 'FALSE')."\n";
    }

} catch (\Exception $e) {
    echo 'Error creating automation setting: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== Process completed ===\n";
