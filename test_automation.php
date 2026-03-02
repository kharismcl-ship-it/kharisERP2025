<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\AutomationSetting;
use Modules\Core\Services\AutomationService;

// Check HR data before testing
echo "=== HR Data Check ===\n";
echo 'Employees: '.DB::table('hr_employees')->count()."\n";
echo 'Leave Types: '.DB::table('hr_leave_types')->count()."\n";
echo 'Leave Balances: '.DB::table('hr_leave_balances')->count()."\n\n";

// Create a test automation record
$automation = AutomationSetting::create([
    'module' => 'HR',
    'action' => 'leave-accrual',
    'company_id' => 1,
    'is_enabled' => true,
    'schedule_type' => 'daily',
    'schedule_value' => null,
    'config' => [],
]);

echo "=== Automation Test ===\n";
echo "Created automation: {$automation->module}.{$automation->action}\n";

// Test the automation service
$automationService = app(AutomationService::class);

try {
    $result = $automationService->executeAutomation('HR', 'leave-accrual', 1);
    echo 'Automation executed: '.($result ? 'SUCCESS' : 'FAILED')."\n";

    if ($result) {
        echo 'Leave balances after automation: '.DB::table('hr_leave_balances')->count()."\n";
        echo "Latest leave balance details:\n";
        $latestBalance = DB::table('hr_leave_balances')->latest()->first();
        print_r($latestBalance);
    }

} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo 'Stack trace: '.$e->getTraceAsString()."\n";
}

// Clean up
$automation->delete();
echo "\n=== Test completed ===\n";
