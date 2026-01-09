<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_performance_reviews';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'performance_cycle_id',
        'employee_id',
        'reviewer_employee_id',
        'rating',
        'comments',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
        ];
    }

    /**
     * Get the company that owns the performance review.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the performance cycle for this review.
     */
    public function performanceCycle()
    {
        return $this->belongsTo(PerformanceCycle::class);
    }

    /**
     * Get the employee being reviewed.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the reviewer employee.
     */
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_employee_id');
    }
}
