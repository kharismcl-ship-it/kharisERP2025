<?php

namespace Modules\HR\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\HR\Models\Employee;

class SyncEmployeeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:sync-employee-users {--force : Force sync even if user already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing employees with user accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! config('hr.auto_create_user_accounts')) {
            $this->warn('Auto-creating user accounts is disabled in config.');
            if (! $this->confirm('Do you want to continue anyway?')) {
                return;
            }
        }

        $employees = Employee::whereNotNull('email')
            ->when(! $this->option('force'), function ($query) {
                return $query->whereNull('user_id');
            })
            ->get();

        $this->info("Found {$employees->count()} employees to sync...");

        $bar = $this->output->createProgressBar($employees->count());
        $bar->start();

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($employees as $employee) {
            try {
                // Check if a user with this email already exists
                $user = User::where('email', $employee->email)->first();

                if (! $user && ! $employee->user_id) {
                    // Generate a random password for the new user
                    $password = Str::random(config('hr.default_password_length', 12));

                    // Create the user
                    $user = User::create([
                        'name' => $employee->full_name,
                        'email' => $employee->email,
                        'password' => Hash::make($password),
                        'current_company_id' => $employee->company_id,
                    ]);

                    $created++;
                } elseif ($user && ! $employee->user_id) {
                    // Associate existing user
                    $employee->update(['user_id' => $user->id]);
                    $created++;
                } else {
                    $skipped++;
                }

                // Sync roles if user exists
                if ($employee->user) {
                    $employee->syncUserRoles();
                }
            } catch (\Exception $e) {
                $this->error("Error syncing employee {$employee->id}: {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Sync complete: {$created} users created, {$skipped} skipped, {$errors} errors.");
    }
}
