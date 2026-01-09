<?php

namespace Modules\Hostels\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class HostelFloor extends Model {
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
    }
