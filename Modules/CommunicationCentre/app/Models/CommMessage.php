<?php

namespace Modules\CommunicationCentre\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommMessage extends Model
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
        'provider',
        'template_id',
        'to_name',
        'to_email',
        'to_phone',
        'subject',
        'body',
        'status',
        'error_message',
        'provider_message_id',
        'scheduled_at',
        'sent_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity that owns this message.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the company that owns this message.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the template used for this message.
     */
    public function template()
    {
        return $this->belongsTo(CommTemplate::class, 'template_id');
    }

    /**
     * Get the provider configuration for this message.
     */
    public function providerConfig()
    {
        return $this->belongsTo(CommProviderConfig::class, 'provider', 'provider')
            ->where('channel', $this->channel);
    }
}
