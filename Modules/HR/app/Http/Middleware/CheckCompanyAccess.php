<?php

namespace Modules\HR\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\HR\Models\Employee;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $companyType = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Get the employee record for this user
        $employee = Employee::where('user_id', $user->id)->first();

        if (! $employee) {
            // User is not an employee
            abort(403, 'You do not have employee access.');
        }

        // Get the current company from the session or user's current company
        $currentCompanyId = session('current_company_id', $user->current_company_id);

        // Check if employee is assigned to the current company
        if (! $employee->isAssignedToCompany($currentCompanyId)) {
            abort(403, 'You do not have access to this company.');
        }

        // If company type is specified, check it
        if ($companyType) {
            $company = $employee->company;
            if ($company && $company->company_type !== $companyType) {
                abort(403, 'You do not have access to this company type.');
            }
        }

        return $next($request);
    }
}
