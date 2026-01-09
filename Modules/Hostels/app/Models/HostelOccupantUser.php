<?php

namespace Modules\Hostels\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class HostelOccupantUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'hostel_occupant_users';

    protected $fillable = [
        'hostel_occupant_id',
        'email',
        'password',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function hostelOccupant()
    {
        return $this->belongsTo(HostelOccupant::class, 'hostel_occupant_id');
    }
}
