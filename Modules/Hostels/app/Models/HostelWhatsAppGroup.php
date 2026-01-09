<?php

namespace Modules\Hostels\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelWhatsAppGroup extends Model
{
    use HasFactory;

    protected $table = 'hostel_whatsapp_groups';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'hostel_id',
        'name',
        'group_id',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the WhatsApp group.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the hostel that owns the WhatsApp group.
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    protected static function newFactory()
    {
        return \Modules\Hostels\Database\Factories\HostelWhatsAppGroupFactory::new();
    }

    /**
     * Get the occupants (participants) of this WhatsApp group.
     */
    public function occupants()
    {
        return $this->belongsToMany(HostelOccupant::class, 'hostel_whatsapp_group_occupant', 'whatsapp_group_id', 'hostel_occupant_id');
    }

    /**
     * Get the messages sent to this WhatsApp group.
     */
    public function messages()
    {
        return $this->hasMany(WhatsAppGroupMessage::class, 'whatsapp_group_id');
    }
}
