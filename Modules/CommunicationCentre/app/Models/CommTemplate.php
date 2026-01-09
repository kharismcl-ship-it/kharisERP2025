<?php

namespace Modules\CommunicationCentre\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'code',
        'channel',
        'name',
        'subject',
        'body',
        'description',
        'is_active',
        'provider_config_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns this template.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the messages that used this template.
     */
    public function messages()
    {
        return $this->hasMany(CommMessage::class, 'template_id');
    }

    /**
     * Get the provider configuration for this template.
     */
    public function providerConfig()
    {
        return $this->belongsTo(CommProviderConfig::class, 'provider_config_id');
    }
}
