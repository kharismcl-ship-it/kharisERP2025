<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Core\Services\AutomationService;

$automationService = app(AutomationService::class);

// Test the handler class name generation
$action = 'leave_accrual';

// Test the transformation manually (since getHandlerClassName is protected)
$handlerClassName = str_replace('_', '', ucwords($action, '_'));
$fullHandlerClass = "Modules\\HR\\Services\\Automation\\{$handlerClassName}Handler";

echo "Action: {$action}\n";
echo "Handler class name: {$handlerClassName}\n";
echo "Full handler class: {$fullHandlerClass}\n";
echo 'Class exists: '.(class_exists($fullHandlerClass) ? 'YES' : 'NO')."\n";

if (class_exists($fullHandlerClass)) {
    echo "Class methods: \n";
    $methods = get_class_methods($fullHandlerClass);
    print_r($methods);
} else {
    echo "Trying alternative class names...\n";

    // Try different variations
    $variations = [
        'Modules\\HR\\Services\\Automation\\LeaveAccrualHandler',
        'Modules\\HR\\Services\\Automation\\leave_accrualHandler',
        'Modules\\HR\\Services\\Automation\\LeaveAccrual',
    ];

    foreach ($variations as $variation) {
        echo "Checking: {$variation} - ".(class_exists($variation) ? 'EXISTS' : 'NOT EXISTS')."\n";
    }
}
