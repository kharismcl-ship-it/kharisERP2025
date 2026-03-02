<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check all provider configurations
echo "=== ALL PROVIDER CONFIGURATIONS ===\n\n";

$providers = DB::table('comm_provider_configs')->get();

foreach ($providers as $provider) {
    echo 'ID: '.$provider->id."\n";
    echo 'Company ID: '.($provider->company_id ?? 'NULL')."\n";
    echo 'Channel: '.$provider->channel."\n";
    echo 'Provider: '.$provider->provider."\n";
    echo 'Name: '.$provider->name."\n";
    echo 'Is Default: '.($provider->is_default ? 'Yes' : 'No')."\n";
    echo 'Is Active: '.($provider->is_active ? 'Yes' : 'No')."\n";
    echo "------------------------\n";
}

// Check specifically for company-specific defaults
echo "\n=== COMPANY-SPECIFIC DEFAULTS ===\n\n";

$companyDefaults = DB::table('comm_provider_configs')
    ->whereNotNull('company_id')
    ->where('is_default', 1)
    ->get();

if ($companyDefaults->count() > 0) {
    foreach ($companyDefaults as $provider) {
        echo "Found company-specific default:\n";
        echo 'Company ID: '.$provider->company_id."\n";
        echo 'Channel: '.$provider->channel."\n";
        echo 'Provider: '.$provider->provider."\n";
        echo "------------------------\n";
    }
} else {
    echo "No company-specific defaults found.\n";
}

// Check global defaults
echo "\n=== GLOBAL DEFAULTS (company_id = NULL) ===\n\n";

$globalDefaults = DB::table('comm_provider_configs')
    ->whereNull('company_id')
    ->where('is_default', 1)
    ->get();

if ($globalDefaults->count() > 0) {
    foreach ($globalDefaults as $provider) {
        echo "Found global default:\n";
        echo 'Channel: '.$provider->channel."\n";
        echo 'Provider: '.$provider->provider."\n";
        echo "------------------------\n";
    }
} else {
    echo "No global defaults found.\n";
}
