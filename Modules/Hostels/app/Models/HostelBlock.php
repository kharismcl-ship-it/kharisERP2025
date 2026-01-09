<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelBlock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_blocks';

    protected $fillable = [
        'hostel_id',
        'name',
        'gender_option',
        'description',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function floors()
    {
        return $this->hasMany(HostelFloor::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'block_id');
    }
}
