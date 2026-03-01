<?php

namespace App\Filament\CompanyAdmin\Pages;

use App\Models\Company;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public function mount(): void
    {
        // Get the user's current company
        $user = Auth::user();
        $currentCompanyId = $user->current_company_id;

        // If user has a current company selected, redirect to the main dashboard
        // Filament will handle the tenant context automatically through middleware
        if ($currentCompanyId) {
            $company = Company::find($currentCompanyId);

            // Verify user has access to this company
            if ($company && $user->canAccessTenant($company)) {
                // Redirect to the main dashboard - Filament's tenant middleware will handle the context
                $this->redirect('/company-admin');

                return;
            }
        }

        // Check if user has any companies assigned at all
        $userCompanies = $user->companies;

        if ($userCompanies->isEmpty()) {
            // User has no companies assigned - show appropriate error
            abort(403, 'You are not assigned to any company. Please contact your administrator to be assigned to a company.');
        }

        // User has companies but no current_company_id selected
        // This should be handled by UserResource to set a default company
        abort(403, 'No company selected. Please contact your administrator to set a default company for your account.');
    }

    public static function getNavigationLabel(): string
    {
        return 'Company Dashboard';
    }
}
