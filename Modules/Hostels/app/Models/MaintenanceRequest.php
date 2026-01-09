<?php

namespace Modules\Hostels\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\Factories\MaintenanceRequestFactory;

class MaintenanceRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_maintenance_requests';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'room_id',
        'bed_id',
        'reported_by_hostel_occupant_id',
        'reported_by_user_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to_user_id',
        'reported_at',
        'completed_at',
    ];

    protected static function newFactory(): MaintenanceRequestFactory
    {
        return MaintenanceRequestFactory::new();
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function reportedByHostelOccupant()
    {
        return $this->belongsTo(HostelOccupant::class, 'reported_by_hostel_occupant_id');
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
