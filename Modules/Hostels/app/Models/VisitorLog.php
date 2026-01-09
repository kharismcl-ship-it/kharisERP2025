<?php

namespace Modules\Hostels\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\VisitorLogFactory;

class VisitorLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_visitor_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'hostel_occupant_id',
        'visitor_name',
        'visitor_phone',
        'purpose',
        'check_in_at',
        'check_out_at',
        'recorded_by_user_id',
    ];

    protected static function newFactory(): VisitorLogFactory
    {
        return VisitorLogFactory::new();
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function occupant()
    {
        return $this->belongsTo(HostelOccupant::class);
    }

    public function recordedByUser()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
