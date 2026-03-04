<?php

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class RequisitionApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'employee_id',
        'role',
        'decision',
        'decided_at',
        'comment',
        'signature',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
