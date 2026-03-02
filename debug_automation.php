<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Core\Models\AutomationSetting;
use Modules\Core\Services\AutomationService;

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Debug Automation Execution ===\n\n";

// Check if the handler class exists
$handlerClass = 'Modules\\HR\\Services\\Automation\\LeaveAccrualHandler';
echo "Checking handler class: {$handlerClass}\n";
echo 'Class exists: '.(class_exists($handlerClass) ? 'YES' : 'NO')."\n\n";

// Check if the handler has the execute method
if (class_exists($handlerClass)) {
    $handler = app($handlerClass);
    echo 'Handler has execute method: '.(method_exists($handler, 'execute') ? 'YES' : 'NO')."\n\n";
}

// Create automation setting
$setting = new AutomationSetting([
    'module' => 'HR',
    'action' => 'leave_accrual',
    'company_id' => 1,
    'config' => [],
]);

echo "Automation Setting:\n";
echo "- Module: {$setting->module}\n";
echo "- Action: {$setting->action}\n";
echo "- Company ID: {$setting->company_id}\n\n";

// Test the automation service directly
try {
    $automationService = app(AutomationService::class);

    echo "Executing automation service...\n";
    $result = $automationService->executeAutomation('HR', 'leave_accrual', 1);

    echo 'Result: '.print_r($result, true)."\n";

} catch (\Exception $e) {
    echo "\n=== ERROR DETAILS ===\n";
    echo 'Message: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}

echo "\n=== Debug completed ===\n";
