<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Verifying Leave Balances After Automation ===\n\n";

// Check current leave balances
try {
    echo "Current Leave Balances:\n";
    $balances = DB::table('hr_leave_balances')->get();

    foreach ($balances as $balance) {
        echo "- Balance ID: {$balance->id}\n";
        echo "  Employee ID: {$balance->employee_id}\n";
        echo "  Leave Type ID: {$balance->leave_type_id}\n";
        echo "  Year: {$balance->year}\n";
        echo "  Current Balance: {$balance->current_balance}\n";
        echo "  Adjustments: {$balance->adjustments}\n";
        echo '  Last Calculated: '.($balance->last_calculated_at ?? 'Never')."\n";
        echo '  Notes: '.($balance->notes ?? 'None')."\n\n";
    }

    // Check automation logs
    echo "Automation Logs:\n";
    $logs = DB::table('automation_logs')->get();

    foreach ($logs as $log) {
        echo "- Log ID: {$log->id}\n";
        echo "  Automation Setting ID: {$log->automation_setting_id}\n";
        echo "  Status: {$log->status}\n";
        echo '  Records Processed: '.($log->records_processed ?? 0)."\n";
        echo '  Started At: '.($log->started_at ?? 'Never')."\n";
        echo '  Completed At: '.($log->completed_at ?? 'Not completed')."\n";
        echo '  Error: '.($log->error_message ?? 'None')."\n\n";
    }

} catch (\Exception $e) {
    echo 'Error checking leave balances: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n\n";
}

echo "\n=== Verification completed ===\n";
