<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shield:super-admin-gate 
                            {--user-id= : The ID of the user to assign as super admin}
                            {--email= : The email of the user to assign as super admin}
                            {--panel=admin : The panel ID to use for configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign super admin role to a user using Filament Shield\'s configuration';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userId = $this->option('user-id');
        $email = $this->option('email');
        $panel = $this->option('panel');

        if (! $userId && ! $email) {
            $this->error('Please provide either --user-id or --email option');

            return;
        }

        // Find user by ID or email
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } elseif ($email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $this->error('User not found');

            return;
        }

        // Get Filament Shield configuration
        $shieldConfig = config('filament-shield');

        if (! $shieldConfig) {
            $this->error('Filament Shield configuration not found');

            return;
        }

        $superAdminRoleName = $shieldConfig['super_admin']['name'] ?? 'super_admin';

        // Ensure super_admin role exists with proper company_id (null for super admin)
        $superAdminRole = Role::firstOrCreate(
            ['name' => $superAdminRoleName, 'guard_name' => 'web'],
            ['name' => $superAdminRoleName, 'guard_name' => 'web', 'company_id' => null]
        );

        // Remove any existing roles that might conflict
        $user->roles()->where('company_id', '!=', null)->detach();

        // Assign the super admin role
        $user->assignRole($superAdminRole);

        $this->info("Super admin role '{$superAdminRoleName}' assigned to user: {$user->email} (ID: {$user->id})");
        $this->info("Using Filament Shield configuration from panel: {$panel}");

        // Check if gate-based super admin is enabled
        if ($shieldConfig['super_admin']['define_via_gate'] ?? false) {
            $this->info('Gate-based super admin configuration is active');
        } else {
            $this->info('Role-based super admin configuration is active');
        }
    }
}
