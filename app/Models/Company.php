<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class Company extends Model implements HasAvatar
{
    use HasFactory;

    /**
     * Cache TTL in seconds for descendant ID lookups.
     * Short enough to pick up new subsidiaries quickly.
     */
    const DESCENDANT_CACHE_TTL = 300;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'company_logo',
        'company_service_type',
        'company_service_description',
        'company_address',
        'company_country',
        'company_city',
        'company_location',
        'company_latitude',
        'company_longitude',
        'company_ghanapostgps',
        'company_phone',
        'company_email',
        'company_website',
        'is_active',
        'parent_company_id',
    ];

    protected function casts()
    {
        return [
            'is_active' => 'boolean',
            'company_location' => 'array',
            'company_latitude' => 'double',
            'company_longitude' => 'double',

        ];
    }

    /**
     * Users that belong to the company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot(['position', 'is_active', 'assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Active users only.
     */
    public function activeUsers()
    {
        return $this->users()->wherePivot('is_active', true);
    }

    /**
     * Get the payment provider configurations for the company.
     */
    public function payProviderConfigs()
    {
        return $this->hasMany(PayProviderConfig::class, 'company_id');
    }

    // /**
    //  * Get employees assigned to this company.
    //  */
    // public function assignedEmployees()
    // {
    //     return $this->belongsToMany(
    //         \Modules\HR\Models\Employee::class,
    //         'employee_company_assignments',
    //         'company_id',
    //         'employee_id'
    //     )->wherePivot('is_active', true);
    // }

    /**
     * Get all employee assignments for this company.
     */
    public function employeeAssignments()
    {
        return $this->hasMany(\Modules\HR\Models\EmployeeCompanyAssignment::class, 'company_id');
    }

    /**
     * Automation settings for the company.
     */
    public function automationSettings()
    {
        return $this->hasMany(\Modules\Core\Models\AutomationSetting::class, 'company_id');
    }

    public function parentCompany()
    {
        return $this->belongsTo(self::class, 'parent_company_id');
    }

    /**
     * Direct subsidiaries of this company.
     */
    public function childCompanies()
    {
        return $this->hasMany(self::class, 'parent_company_id');
    }

    /**
     * Whether this company is a group/headquarters that owns subsidiaries.
     * An HQ company (type = 'hq') is considered a group company.
     */
    public function isGroupCompany(): bool
    {
        return $this->type === 'hq';
    }

    /**
     * Returns [this company's ID] + all descendant company IDs (recursive).
     * Result is cached per company for DESCENDANT_CACHE_TTL seconds.
     * Call Company::clearDescendantCache($id) when the hierarchy changes.
     */
    public function selfAndDescendantIds(): array
    {
        return Cache::remember(
            "company_{$this->id}_descendant_ids",
            self::DESCENDANT_CACHE_TTL,
            function () {
                $ids = [$this->id];
                $this->collectDescendantIds($this->id, $ids);
                return $ids;
            }
        );
    }

    /**
     * Recursively collect all descendant company IDs into the $ids array.
     */
    private function collectDescendantIds(int $parentId, array &$ids): void
    {
        $children = static::where('parent_company_id', $parentId)
            ->pluck('id')
            ->all();

        foreach ($children as $childId) {
            if (! in_array($childId, $ids)) {
                $ids[] = $childId;
                $this->collectDescendantIds($childId, $ids);
            }
        }
    }

    /**
     * Clear the cached descendant IDs for a company (and all its ancestors).
     * Call this whenever parent_company_id changes on any company record.
     */
    public static function clearDescendantCache(int $companyId): void
    {
        Cache::forget("company_{$companyId}_descendant_ids");

        // Also clear parent chain so their cached lists are rebuilt
        $parent = static::select('id', 'parent_company_id')
            ->find($companyId)?->parentCompany;

        while ($parent) {
            Cache::forget("company_{$parent->id}_descendant_ids");
            $parent = $parent->parentCompany;
        }
    }

    /**
     * Boot — auto-clear descendant cache when parent assignment changes.
     */
    protected static function booted(): void
    {
        static::saved(function (Company $company) {
            if ($company->wasChanged('parent_company_id')) {
                // Clear old parent's cache
                $oldParentId = $company->getOriginal('parent_company_id');
                if ($oldParentId) {
                    static::clearDescendantCache((int) $oldParentId);
                }
                // Clear new parent's cache
                if ($company->parent_company_id) {
                    static::clearDescendantCache((int) $company->parent_company_id);
                }
                // Clear own cache
                static::clearDescendantCache($company->id);
            }
        });

        static::deleted(function (Company $company) {
            if ($company->parent_company_id) {
                static::clearDescendantCache((int) $company->parent_company_id);
            }
            static::clearDescendantCache($company->id);
        });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! empty($this->company_logo)) {
            return Storage::url($this->company_logo);
        }

        return null;
    }
}
