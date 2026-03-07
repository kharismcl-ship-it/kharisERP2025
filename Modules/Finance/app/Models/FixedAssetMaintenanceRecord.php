<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAssetMaintenanceRecord extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'maintenance_type',
        'title',
        'description',
        'scheduled_date',
        'completed_date',
        'cost',
        'contractor',
        'next_due_date',
        'status',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'next_due_date'  => 'date',
        'cost'           => 'decimal:2',
    ];

    public const TYPES = [
        'preventive'  => 'Preventive',
        'corrective'  => 'Corrective',
        'inspection'  => 'Inspection',
        'calibration' => 'Calibration',
        'overhaul'    => 'Overhaul',
        'other'       => 'Other',
    ];

    public const STATUSES = [
        'scheduled'   => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }
}