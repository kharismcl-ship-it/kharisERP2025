<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: companies the user belongs to.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot(['position', 'is_active', 'assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Relationship: the user's current company.
     */
    public function currentCompany()
    {
        return $this->belongsTo(Company::class, 'current_company_id');
    }

    /**
     * Convenience: current company id from session or container.
     */
    public function currentCompanyId(): ?int
    {
        return app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');
    }

    /**
     * Get the user's initials
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
     * Relationship: company assignments through pivot table.
     */
    // public function companyAssignments()
    // {
    //     return $this->hasMany(CompanyUser::class, 'user_id');
    // }

    /**
     * Check if user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Adjust this based on your authorization logic
    }

    /**
     * Get the tenants (companies) that the user belongs to.
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->companies;
    }

    /**
     * Check if user can access a specific tenant (company).
     */
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->companies()->whereKey($tenant->getKey())->exists();
    }
}
