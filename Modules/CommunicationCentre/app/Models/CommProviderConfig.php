<?php

namespace Modules\CommunicationCentre\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommProviderConfig extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CommProviderConfig::factory()
            ->count(1)
            ->make();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'channel',
        'provider',
        'name',
        'config',
        'is_active',
        'is_default',
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
