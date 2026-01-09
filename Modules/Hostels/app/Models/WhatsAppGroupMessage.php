<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppGroupMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'whatsapp_group_messages';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'whatsapp_group_id',
        'sender_hostel_occupant_id',
        'message_type',
        'content',
        'media_url',
        'sent_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the WhatsApp group this message was sent to.
     */
    public function whatsappGroup()
    {
        return $this->belongsTo(HostelWhatsAppGroup::class, 'whatsapp_group_id');
    }

    /**
     * Get the hostel occupant who sent this message.
     */
    public function sender()
    {
        return $this->belongsTo(HostelOccupant::class, 'sender_hostel_occupant_id');
    }
}
