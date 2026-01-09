<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelPayroll extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_payroll';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'hostel_id',
        'period_start_date',
        'period_end_date',
        'total_staff_count',
        'total_hours_worked',
        'total_payroll_amount',
        'status',
        'synced_at',
        'exported_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'synced_at' => 'datetime',
        'exported_at' => 'datetime',
        'total_payroll_amount' => 'decimal:2',
    ];

    /**
     * Get the hostel that owns the payroll.
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Modules\Hostels\Database\Factories\HostelPayrollFactory::new();
    }
}
