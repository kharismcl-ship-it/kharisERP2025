<?php

namespace Modules\CommunicationCentre\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'notifiable_type',
        'notifiable_id',
        'channel',
        'is_enabled',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the notifiable entity that owns this preference.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the company that owns this preference.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
