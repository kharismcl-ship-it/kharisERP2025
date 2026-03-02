<?php

use Illuminate\Support\Facades\DB;

// Update existing leave types with proper accrual configuration
echo "Updating leave types with accrual configuration...\n";

// Update annual leave (20 days per year = 1.67 days per month)
DB::table('hr_leave_types')
    ->where('name', 'like', '%annual%')
    ->update([
        'has_accrual' => true,
        'accrual_rate' => 1.67,
        'accrual_frequency' => 'monthly',
    ]);

// Update sick leave (12 days per year = 1 day per month)
DB::table('hr_leave_types')
    ->where('name', 'like', '%sick%')
    ->update([
        'has_accrual' => true,
        'accrual_rate' => 1.00,
        'accrual_frequency' => 'monthly',
    ]);

// Update casual leave (7 days per year = 0.58 days per month)
DB::table('hr_leave_types')
    ->where('name', 'like', '%casual%')
    ->update([
        'has_accrual' => true,
        'accrual_rate' => 0.58,
        'accrual_frequency' => 'monthly',
    ]);

// Disable accrual for special leave types if needed
DB::table('hr_leave_types')
    ->where('name', 'like', '%special%')
    ->orWhere('name', 'like', '%unpaid%')
    ->update([
        'has_accrual' => false,
        'accrual_rate' => 0.00,
    ]);

echo "Leave types updated successfully!\n";

// Show the updated leave types
$leaveTypes = DB::table('hr_leave_types')->get();
echo "\nUpdated Leave Types:\n";
foreach ($leaveTypes as $type) {
    echo "- {$type->name}: has_accrual={$type->has_accrual}, rate={$type->accrual_rate}, frequency={$type->accrual_frequency}\n";
}
