<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Modules\Core\Services\AutomationService;

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Comprehensive Accrual Scenarios Test ===\n\n";

// Check current data
echo "Current HR Data:\n";
echo 'HR Employees: '.DB::table('hr_employees')->count()."\n";
echo 'HR Leave Types: '.DB::table('hr_leave_types')->count()."\n";
echo 'HR Leave Balances: '.DB::table('hr_leave_balances')->count()."\n\n";

// Show current leave types configuration
$leaveTypes = DB::table('hr_leave_types')->get();
echo "Leave Types Configuration:\n";
foreach ($leaveTypes as $type) {
    echo "- {$type->name}: has_accrual={$type->has_accrual}, rate={$type->accrual_rate}, frequency={$type->accrual_frequency}\n";
}
echo "\n";

// Show current leave balances
$balances = DB::table('hr_leave_balances')->get();
echo "Current Leave Balances:\n";
foreach ($balances as $balance) {
    echo "- Employee ID: {$balance->employee_id}, Leave Type: {$balance->leave_type_id}, Balance: {$balance->current_balance}\n";
}
echo "\n";

// Test the automation service
$automationService = app(AutomationService::class);

try {
    echo "Executing leave accrual automation...\n";
    $result = $automationService->executeAutomation('HR', 'leave_accrual', 1);

    echo 'Automation result: '.($result ? 'SUCCESS' : 'FAILED')."\n";

    if ($result) {
        echo 'Leave balances after automation: '.DB::table('hr_leave_balances')->count()."\n";

        // Show updated balances
        $updatedBalances = DB::table('hr_leave_balances')->get();
        echo "Updated Leave Balances:\n";
        foreach ($updatedBalances as $balance) {
            echo "- Employee ID: {$balance->employee_id}, Leave Type: {$balance->leave_type_id}, Balance: {$balance->current_balance}\n";
        }
    }

} catch (\Exception $e) {
    echo "\n=== ERROR DETAILS ===\n";
    echo 'Message: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== Test completed ===\n";
