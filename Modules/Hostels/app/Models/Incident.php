<?php

namespace Modules\Hostels\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\IncidentFactory;

class Incident extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_incidents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'hostel_occupant_id',
        'room_id',
        'title',
        'description',
        'severity',
        'reported_by_user_id',
        'action_taken',
        'status',
        'reported_at',
        'resolved_at',
    ];

    protected static function newFactory(): IncidentFactory
    {
        return IncidentFactory::new();
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function occupant()
    {
        return $this->belongsTo(HostelOccupant::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }
}
