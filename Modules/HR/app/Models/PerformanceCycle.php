<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceCycle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_performance_cycles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the company that owns the performance cycle.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the performance reviews for this cycle.
     */
    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }
}
