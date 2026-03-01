<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateLeaveTypesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:update-leave-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leave types with proper accrual configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating leave types with accrual configuration...');

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

        $this->info('Leave types updated successfully!');

        // Show the updated leave types
        $leaveTypes = DB::table('hr_leave_types')->get();
        $this->info('\nUpdated Leave Types:');
        foreach ($leaveTypes as $type) {
            $this->line("- {$type->name}: has_accrual={$type->has_accrual}, rate={$type->accrual_rate}, frequency={$type->accrual_frequency}");
        }
    }
}
