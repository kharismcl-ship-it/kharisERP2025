<?php

namespace Modules\PaymentsChannel\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayProviderConfig extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'provider',
        'name',
        'is_default',
        'is_active',
        'mode',
        'config',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'config' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns this provider config.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
