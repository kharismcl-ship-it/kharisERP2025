<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelMovieRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_occupant_id',
        'hostel_id',
        'title',
        'description',
        'urgency',
        'status',
        'fulfilled_movie_id',
        'is_private',
        'admin_notes',
    ];

    public function occupant()
    {
        return $this->belongsTo(HostelOccupant::class, 'hostel_occupant_id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function fulfilledMovie()
    {
        return $this->belongsTo(HostelMovie::class, 'fulfilled_movie_id');
    }
}
