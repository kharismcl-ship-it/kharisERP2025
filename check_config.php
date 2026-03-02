<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check the communication centre configuration
echo "Communication Centre Configuration:\n";
$channels = config('communicationcentre.channels', []);
echo 'Available channels: '.json_encode($channels)."\n";

// Check default channels from NotificationPreferenceForm
echo "Default channels in form: ['email', 'database']\n";

// Let's test creating some preferences manually
use Modules\CommunicationCentre\Models\CommPreference;
use Modules\HR\Models\Employee;

$employee = Employee::find(1);
if ($employee) {
    echo "\nTesting manual preference creation for employee ID 1:\n";

    // Create some enabled preferences
    $channelsToEnable = ['email', 'whatsapp'];

    foreach ($channelsToEnable as $channel) {
        $pref = CommPreference::create([
            'company_id' => $employee->company_id ?? 1,
            'notifiable_type' => 'employee',
            'notifiable_id' => $employee->id,
            'channel' => $channel,
            'is_enabled' => true,
        ]);
        echo 'Created preference: '.$channel.' (ID: '.$pref->id.")\n";
    }

    // Now check if they show up
    $enabledPrefs = CommPreference::where('notifiable_type', 'employee')
        ->where('notifiable_id', $employee->id)
        ->where('is_enabled', true)
        ->pluck('channel')
        ->toArray();

    echo "\nEnabled preferences after creation: ".json_encode($enabledPrefs)."\n";

    // Test the display logic
    if (empty($enabledPrefs)) {
        echo "Display would show: All channels enabled\n";
    } else {
        $channelLabels = [
            'email' => 'Email',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp',
            'database' => 'In-App',
        ];

        $enabledChannels = array_map(function ($channel) use ($channelLabels) {
            return $channelLabels[$channel] ?? ucfirst($channel);
        }, $enabledPrefs);

        echo 'Display would show: '.implode(', ', $enabledChannels)."\n";
    }
}
