<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
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

class User extends Authenticatable implements FilamentUser, HasTenants
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
     * Filament panel access control.
     *
     * - admin panel      → only global super_admin users
     * - company-admin    → any user with at least one active company assignment
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isGlobalSuperAdmin();
        }

        // company-admin panel: user must belong to at least one company
        return $this->activeCompanies()->exists();
    }

    /**
     * Return the list of companies (tenants) this user can switch between
     * in the company-admin panel.
     */
    public function getTenants(Panel $panel): Collection
    {
        // Global super admins see ALL companies so they can manage any tenant
        if ($this->isGlobalSuperAdmin()) {
            return Company::all();
        }

        return $this->activeCompanies()->get();
    }

    /**
     * Authorise access to a specific tenant.
     * Global super admins bypass the membership check.
     */
    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isGlobalSuperAdmin()) {
            return true;
        }

        return $this->activeCompanies()->whereKey($tenant->getKey())->exists();
    }

    /**
     * Check whether this user holds a global (non-company-scoped) super_admin role.
     * We query the DB directly to avoid team-mode interference.
     */
    public function isGlobalSuperAdmin(): bool
    {
        $teamKey = config('permission.column_names.team_foreign_key', 'team_id');

        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', get_class($this))
            ->where('model_has_roles.model_id', $this->getKey())
            ->where('roles.name', 'super_admin')
            ->whereNull("model_has_roles.{$teamKey}")
            ->exists();
    }

    // ─── Communication centre helpers ────────────────────────────────────────

    public function getCommName(): string   { return $this->name; }
    public function getCommEmail(): ?string { return $this->email; }
    public function getCommPhone(): ?string { return null; }
}
