<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\CommunicationCentre\Models\CommProviderConfig;

// Create database provider for Company ID 1
echo "Creating database provider for Company ID 1...\n";

$dbProvider = CommProviderConfig::create([
    'company_id' => 1,
    'channel' => 'database',
    'provider' => 'FilamentDatabaseProvider',
    'name' => 'Filament Database Notifications',
    'is_default' => true,
    'is_active' => true,
    'config' => json_encode([]),
]);

echo "✅ Database provider created successfully!\n";
echo 'ID: '.$dbProvider->id."\n";
echo 'Company ID: '.$dbProvider->company_id."\n";
echo 'Channel: '.$dbProvider->channel."\n";
echo 'Provider: '.$dbProvider->provider."\n";
echo 'Default: '.($dbProvider->is_default ? 'Yes' : 'No')."\n";
echo 'Active: '.($dbProvider->is_active ? 'Yes' : 'No')."\n";

// Also create a global fallback
echo "\nCreating global fallback database provider...\n";

$globalDbProvider = CommProviderConfig::create([
    'company_id' => null,
    'channel' => 'database',
    'provider' => 'FilamentDatabaseProvider',
    'name' => 'Global Database Notifications',
    'is_default' => true,
    'is_active' => true,
    'config' => json_encode([]),
]);

echo "✅ Global database provider created successfully!\n";
