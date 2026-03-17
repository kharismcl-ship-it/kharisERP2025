<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class VendorContact extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'vendor_contacts';

    protected $fillable = [
        'vendor_id',
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'job_title',
        'is_primary',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active'  => 'boolean',
        'password'   => 'hashed',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
