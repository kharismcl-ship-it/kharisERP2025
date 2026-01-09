<?php

namespace Modules\HR\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\HR\Models\Employee;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Automatically create a user account when an employee is created
        if (config('hr.auto_create_user_accounts') && $employee->email && ! $employee->user_id) {
            $this->createUserFromEmployee($employee);
        }
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

        // If employee is updated with an email but doesn't have a user, create one
        if (config('hr.auto_create_user_accounts') && $employee->email && ! $employee->user_id) {
            $this->createUserFromEmployee($employee);
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
