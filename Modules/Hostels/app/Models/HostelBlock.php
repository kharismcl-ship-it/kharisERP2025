<?php

namespace Modules\Hostels\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class HostelBlock extends Model {
        public function hostel(): BelongsTo
        {
        return $this->belongsTo(Hostel::class);
        }
    }
