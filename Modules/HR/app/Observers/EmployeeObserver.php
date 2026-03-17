<?php

namespace Modules\HR\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\HR\Events\NewEmployeeOnboarded;
use Modules\HR\Models\Employee;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Automatic user creation is now disabled by default
        // Use the hybrid approach with request/approve workflow instead
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        // If employee email is updated and they have a user account, update the user too
        if ($employee->isDirty('email') && $employee->user) {
            $employee->user->update([
                'email' => $employee->email,
                'name' => $employee->full_name,
            ]);
        }

        // Fire onboarding event when user account is first linked (system access granted)
        if ($employee->isDirty('user_id') && $employee->user_id && ! $employee->getOriginal('user_id')) {
            event(new NewEmployeeOnboarded($employee));
        }
    }

    /**
     * Create a user account from employee details.
     */
    protected function createUserFromEmployee(Employee $employee): void
    {
        // Check if a user with this email already exists
        $user = User::where('email', $employee->email)->first();

        if (! $user) {
            // Generate a random password for the new user
            $password = Str::random(config('hr.default_password_length', 12));

            // Create the user
            $user = User::create([
                'name' => $employee->full_name,
                'email' => $employee->email,
                'password' => Hash::make($password),
                'current_company_id' => $employee->company_id,
            ]);

            // Send notification with credentials (in a real app, you'd send an email)
            // This is just a placeholder for the actual notification
            // Notification::send($user, new EmployeeAccountCreated($user, $password));
        }

        // Associate the employee with the user
        $employee->update(['user_id' => $user->id]);

        // Sync roles based on company assignments
        $employee->syncUserRoles();
    }
}
