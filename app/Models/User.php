<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Kirschbaum\Commentions\Contracts\Commenter;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Modules\CommunicationCentre\Traits\HasNotificationPreferences;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants, Commenter
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasNotificationPreferences, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_company_id',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'current_company_id' => 'integer',
        ];
    }

    /**
     * All companies this user belongs to (with pivot metadata).
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot(['position', 'is_active', 'assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Only actively assigned companies.
     */
    public function activeCompanies()
    {
        return $this->companies()->wherePivot('is_active', true);
    }

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * The employee profile linked to this user account (if any).
     */
    public function employee()
    {
        return $this->hasOne(\Modules\HR\Models\Employee::class, 'user_id');
    }

    /**
     * Filament panel access control.
     *
     * - admin panel      → only global super_admin users
     * - company-admin    → any user with at least one active company assignment
     * - staff            → any user with at least one active company assignment
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isGlobalSuperAdmin();
        }

        if (in_array($panel->getId(), ['company-admin', 'staff'])) {
            return $this->activeCompanies()->exists();
        }

        return false;
    }

    /**
     * Return the list of companies (tenants) this user can switch between
     * in the company-admin panel.
     *
     * - Global super_admins see every company.
     * - Regular users see their directly assigned active companies only.
     *   The UI tenant switcher shows these as selectable tenants; when a
     *   group/HQ company is selected, the TenantScope automatically expands
     *   queries to include all of that group's subsidiaries.
     */
    public function getTenants(Panel $panel): Collection
    {
        if ($this->isGlobalSuperAdmin()) {
            return Company::all();
        }

        return $this->activeCompanies()->get();
    }

    /**
     * Authorise access to a specific tenant.
     *
     * - Global super_admins bypass all checks.
     * - Users assigned to a parent/HQ company are also authorised to access
     *   any of that company's subsidiaries (they inherit group access).
     */
    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isGlobalSuperAdmin()) {
            return true;
        }

        // Direct membership check
        if ($this->activeCompanies()->whereKey($tenant->getKey())->exists()) {
            return true;
        }

        // Hierarchy check: if the user belongs to a group/HQ company that is
        // an ancestor of the requested tenant, grant access.
        $userCompanyIds = $this->activeCompanies()->pluck('companies.id')->all();

        foreach ($userCompanyIds as $userCompanyId) {
            $userCompany = Company::find($userCompanyId);
            if ($userCompany && $userCompany->isGroupCompany()) {
                $descendants = $userCompany->selfAndDescendantIds();
                if (in_array($tenant->getKey(), $descendants)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check whether this user holds a global (non-company-scoped) super_admin role.
     * We query the DB directly to avoid team-mode interference.
     */
    public function isGlobalSuperAdmin(): bool
    {
        $teamKey = config('permission.column_names.team_foreign_key', 'team_id');

        // Check if the user holds the global super_admin ROLE (role.company_id IS NULL).
        // We check the role's company_id, NOT model_has_roles.company_id, because
        // the model_has_roles.company_id column is NOT NULL (part of the PK) and
        // cannot store NULL values in this schema.
        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', get_class($this))
            ->where('model_has_roles.model_id', $this->getKey())
            ->where('roles.name', 'super_admin')
            ->whereNull("roles.{$teamKey}")
            ->exists();
    }

    // ─── Communication centre helpers ────────────────────────────────────────

    public function getCommName(): string   { return $this->name; }
    public function getCommEmail(): ?string { return $this->email; }
    public function getCommPhone(): ?string { return null; }
}
