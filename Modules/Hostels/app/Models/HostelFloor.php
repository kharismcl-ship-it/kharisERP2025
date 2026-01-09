<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelFloor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_floors';

    protected $fillable = [
        'hostel_id',
        'hostel_block_id',
        'name',
        'level',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function hostelBlock(): BelongsTo
    {
        return $this->belongsTo(HostelBlock::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'floor_id');
    }
}
