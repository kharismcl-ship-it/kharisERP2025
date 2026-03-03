<?php

namespace Modules\ITSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class ItTrainingAttendee extends Model
{
    use HasFactory;

    protected $table = 'it_training_attendees';

    protected $fillable = [
        'it_training_session_id',
        'employee_id',
        'attended',
        'feedback',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'attended' => 'boolean',
        ];
    }

    public function session()
    {
        return $this->belongsTo(ItTrainingSession::class, 'it_training_session_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
