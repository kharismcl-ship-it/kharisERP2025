<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class SafetyIncident extends Model
{
    protected $table = 'hr_safety_incidents';

    protected $fillable = [
        'company_id',
        'employee_id',
        'ref_number',
        'incident_date',
        'location',
        'incident_type',
        'description',
        'severity',
        'injury_type',
        'body_part_affected',
        'immediate_action_taken',
        'reported_by_employee_id',
        'status',
        'root_cause',
        'corrective_action',
        'investigated_by_employee_id',
        'closed_at',
        'reported_to_authorities',
    ];

    protected $casts = [
        'incident_date'           => 'datetime',
        'closed_at'               => 'datetime',
        'reported_to_authorities' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model) {
            if (! $model->ref_number) {
                $model->ref_number = 'INC-' . now()->format('Ym') . '-' . str_pad(
                    static::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count() + 1,
                    5, '0', STR_PAD_LEFT
                );
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reported_by_employee_id');
    }

    public function investigatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'investigated_by_employee_id');
    }

    public static function incidentTypes(): array
    {
        return [
            'near_miss'          => 'Near Miss',
            'first_aid'          => 'First Aid',
            'medical_treatment'  => 'Medical Treatment',
            'lost_time'          => 'Lost Time Injury',
            'property_damage'    => 'Property Damage',
            'fatality'           => 'Fatality',
        ];
    }

    public static function severities(): array
    {
        return [
            'minor'    => 'Minor',
            'moderate' => 'Moderate',
            'serious'  => 'Serious',
            'critical' => 'Critical',
            'fatal'    => 'Fatal',
        ];
    }
}