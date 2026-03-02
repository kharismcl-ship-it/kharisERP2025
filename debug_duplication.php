<?php

use Modules\CommunicationCentre\Models\CommPreference;
use Modules\HR\Models\Employee;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$employee = Employee::find(1);
echo "Employee: {$employee->first_name} {$employee->last_name} (ID: {$employee->id})\n\n";

// Check what preferences exist
$preferences = CommPreference::where('notifiable_type', 'employee')
    ->where('notifiable_id', $employee->id)
    ->where('is_enabled', true)
    ->pluck('channel')
    ->toArray();

echo 'Database preferences: '.json_encode($preferences)."\n";

// Simulate the display logic
$channelLabels = [
    'email' => 'Email',
    'sms' => 'SMS',
    'whatsapp' => 'WhatsApp',
    'database' => 'In-App',
];

$enabledChannels = array_map(function ($channel) use ($channelLabels) {
    return $channelLabels[$channel] ?? ucfirst($channel);
}, $preferences);

$result = implode(', ', $enabledChannels);

echo "Formatted result: '{$result}'\n";
echo 'Result type: '.gettype($result)."\n";

echo "\nChecking if there are duplicate preferences in database:\n";
$allPrefs = CommPreference::where('notifiable_type', 'employee')
    ->where('notifiable_id', $employee->id)
    ->get();

foreach ($allPrefs as $pref) {
    echo "- ID: {$pref->id}, Channel: {$pref->channel}, Enabled: ".($pref->is_enabled ? 'Yes' : 'No')."\n";
}
