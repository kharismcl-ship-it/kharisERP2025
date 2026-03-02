<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Core\Models\AutomationSetting;

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Debug Automation Setting ===\n\n";

// Check if automation setting exists
try {
    $setting = AutomationSetting::where('module', 'HR')
        ->where('action', 'leave_accrual')
        ->where('company_id', 1)
        ->first();

    if ($setting) {
        echo "Automation Setting Found:\n";
        echo "- ID: {$setting->id}\n";
        echo "- Module: {$setting->module}\n";
        echo "- Action: {$setting->action}\n";
        echo "- Company ID: {$setting->company_id}\n";
        echo '- Is Enabled: '.($setting->is_enabled ? 'YES' : 'NO')."\n";
        echo '- Config: '.print_r($setting->config, true)."\n";
    } else {
        echo "No automation setting found for HR.leave_accrual with company_id = 1\n";

        // Check if there are any automation settings at all
        $allSettings = AutomationSetting::all();
        echo 'Total automation settings: '.$allSettings->count()."\n";
        foreach ($allSettings as $s) {
            echo "- {$s->module}.{$s->action} (Company: {$s->company_id}, Enabled: ".($s->is_enabled ? 'YES' : 'NO').")\n";
        }
    }

} catch (\Exception $e) {
    echo 'Error checking automation setting: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile()."\n";
    echo 'Line: '.$e->getLine()."\n\n";
}

echo "\n=== Debug completed ===\n";
