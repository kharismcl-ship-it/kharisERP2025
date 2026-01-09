<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelStaffRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hostel_occupant_id',
        'name',
        'slug',
        'description',
        'permissions',
        'base_salary',
        'salary_currency',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'base_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(HostelStaffRoleAssignment::class, 'role_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHostelOccupant($query, $hostelOccupantId)
    {
        return $query->where('hostel_occupant_id', $hostelOccupantId);
    }

    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
