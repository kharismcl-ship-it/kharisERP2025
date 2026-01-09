<?php

namespace Modules\Hostels\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Hostels\Services\PayrollSyncService;

class SyncHostelPayroll extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'hostels:sync-payroll 
                            {--period= : Payroll period in format YYYY-MM or specific dates YYYY-MM-DD:YYYY-MM-DD}
                            {--hostel= : Specific hostel ID to process}
                            {--sync-only : Only sync attendance without calculating payroll}
                            {--calculate-only : Only calculate payroll without syncing attendance}
                            {--export : Export calculated payroll to HR system}';

    /**
     * The console command description.
     */
    protected $description = 'Sync hostel staff attendance and calculate payroll for integration with HR system';

    /**
     * Execute the console command.
     */
    public function handle(PayrollSyncService $payrollService)
    {
        $period = $this->option('period');
        $hostelId = $this->option('hostel');

        // Determine date range
        if ($period && strpos($period, ':') !== false) {
            // Specific date range
            [$startDate, $endDate] = explode(':', $period);
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } elseif ($period) {
            // Monthly period
            $startDate = Carbon::parse($period)->startOfMonth();
            $endDate = Carbon::parse($period)->endOfMonth();
        } else {
            // Default to previous month
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        }

        $this->info("Processing payroll for period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        if ($hostelId) {
            $this->info("Specific hostel ID: {$hostelId}");
        }

        // Sync attendance to HR system
        if (! $this->option('calculate-only')) {
            $this->info('Syncing attendance records to HR system...');
            $syncResults = $payrollService->syncAttendanceToPayroll($startDate, $endDate, $hostelId);

            $this->info('Attendance sync completed:');
            $this->info("- Synced: {$syncResults['synced']} records");
            $this->info("- Skipped: {$syncResults['skipped']} records");

            if (! empty($syncResults['errors'])) {
                $this->warn('- Errors: '.count($syncResults['errors']).' records');
                foreach ($syncResults['errors'] as $error) {
                    $this->error("  Employee {$error['employee_id']} on {$error['date']}: {$error['error']}");
                }
            }
        }

        // Calculate payroll
        if (! $this->option('sync-only')) {
            $this->info('Calculating hostel staff payroll...');
            $payrollData = $payrollService->calculateHostelStaffPayroll($startDate, $endDate, $hostelId);

            $this->info('Payroll calculation completed for '.count($payrollData).' employees');

            // Display payroll summary
            $this->table(
                ['Employee ID', 'Hostel', 'Role', 'Days', 'Hours', 'Basic Salary', 'Overtime', 'Total'],
                array_map(function ($item) {
                    return [
                        $item['employee_id'],
                        $item['hostel_id'],
                        $item['role_name'],
                        $item['worked_days'],
                        $item['total_hours'],
                        number_format($item['basic_salary'], 2),
                        number_format($item['overtime_pay'], 2),
                        number_format($item['total_earnings'], 2),
                    ];
                }, $payrollData)
            );

            // Export to HR system if requested
            if ($this->option('export')) {
                $this->info('Exporting payroll data to HR system...');
                $exportSuccess = $payrollService->exportPayrollToHR($payrollData);

                if ($exportSuccess) {
                    $this->info('✅ Payroll data successfully exported to HR system');
                } else {
                    $this->error('❌ Payroll export failed. Check logs for details.');
                }
            }
        }

        $this->info('Payroll sync process completed successfully!');
    }
}
